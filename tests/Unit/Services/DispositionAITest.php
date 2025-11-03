<?php

namespace Tests\Unit\Services;

use App\Models\Organ;
use App\Models\OrganBuilder;
use App\Models\OrganRebuild;
use App\Services\AI\DispositionAI;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use OpenAI\Contracts\ClientContract;
use Tests\TestCase;

class DispositionAITest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    /** @test */
    public function it_numbers_registers_from_one_every_invocation()
    {
        $client = m::mock(ClientContract::class);
        $ai = new DispositionAITestStub("**Manual\nPrincipal 8'\nGedackt 8'\n", $client, addRegisterNumbers: false);

        $numbered1 = $ai->callAddRegisterNumbers("Principal 8'\nGedackt 8'");
        $this->assertSame("1. Principal 8'\n2. Gedackt 8'", $numbered1);

        $numbered2 = $ai->callAddRegisterNumbers("Flautino 4'\nOctave 2'");
        $this->assertSame("1. Flautino 4'\n2. Octave 2'", $numbered2);
    }

    /** @test */
    public function it_describes_organ_information_when_model_is_missing()
    {
        $client = m::mock(ClientContract::class);
        $ai = new DispositionAITestStub("Principal 8'", $client);

        $info = $ai->callGetOrganInfo();

        $this->assertStringContainsString('Details about builder and construction year are not available.', $info);
    }

    /** @test */
    public function it_mentions_builder_rebuilds_and_year_when_available()
    {
        $client = m::mock(ClientContract::class);

        $organ = new Organ;
        $organ->year_built = 1900;
        $organBuilder = new OrganBuilder;
        $organBuilder->first_name = 'Jan';
        $organBuilder->last_name = 'Novak';
        $organ->setRelation('organBuilder', $organBuilder);

        $rebuildBuilder = new OrganBuilder;
        $rebuildBuilder->first_name = 'Pavel';
        $rebuildBuilder->last_name = 'Svoboda';
        $rebuild = new OrganRebuild(['year_built' => 1950]);
        $rebuild->setRelation('organBuilder', $rebuildBuilder);
        $organ->setRelation('organRebuilds', collect([$rebuild]));

        $ai = new DispositionAITestStub("Principal 8'", $client, $organ);

        $info = $ai->callGetOrganInfo();

        $this->assertStringContainsString("It was built by organ builder 'Jan Novak' in 1900.", $info);
        $this->assertStringContainsString("It was later rebuilt by organ builder 'Pavel Svoboda' in 1950.", $info);
    }

    /** @test */
    public function it_highlights_baroque_context_for_old_organs()
    {
        $client = m::mock(ClientContract::class);

        $organ = new Organ;
        $organ->year_built = 1700;
        $organ->setRelation('organBuilder', null);
        $organ->setRelation('organRebuilds', collect());

        $ai = new DispositionAITestStub("Principal 8'", $client, $organ);

        $info = $ai->callGetOrganInfo();

        $this->assertStringContainsString('South German baroque style', $info);
    }

    /** @test */
    public function it_logs_successful_chat_requests()
    {
        config()->set('custom.ai.retry_attempts', 0);
        config()->set('custom.ai.retry_sleep_ms', 0);
        config()->set('custom.ai.max_response_length', 100);

        $chatMock = m::mock();
        $chatMock->shouldReceive('create')->once()->andReturn((object) [
            'choices' => [
                (object) ['message' => (object) ['content' => 'Result content']],
            ],
        ]);

        $client = m::mock(ClientContract::class);
        $client->shouldReceive('chat')->andReturn($chatMock);

        $ai = new DispositionAITestStub("Principal 8'", $client);

        $content = $ai->callSendChatRequest('test_operation', [
            'messages' => [
                ['role' => 'user', 'content' => 'Prompt'],
            ],
        ]);

        $this->assertSame('Result content', $content);
        $this->assertDatabaseHas('ai_request_logs', [
            'operation' => 'test_operation',
            'success' => true,
        ]);
    }

    /** @test */
    public function it_logs_and_throws_when_response_exceeds_limit()
    {
        config()->set('custom.ai.retry_attempts', 0);
        config()->set('custom.ai.retry_sleep_ms', 0);
        config()->set('custom.ai.max_response_length', 5);

        $chatMock = m::mock();
        $chatMock->shouldReceive('create')->once()->andReturn((object) [
            'choices' => [
                (object) ['message' => (object) ['content' => 'Too long content']],
            ],
        ]);

        $client = m::mock(ClientContract::class);
        $client->shouldReceive('chat')->andReturn($chatMock);

        $ai = new DispositionAITestStub("Principal 8'", $client);

        $this->expectException(\LengthException::class);
        try {
            $ai->callSendChatRequest('long_response_check', [
                'messages' => [
                    ['role' => 'user', 'content' => 'Prompt'],
                ],
            ]);
        }
        finally {
            $this->assertDatabaseHas('ai_request_logs', [
                'operation' => 'long_response_check',
                'success' => false,
            ]);
        }
    }
}

class DispositionAITestStub extends DispositionAI
{
    public function __construct(string $disposition, ClientContract $client, ?Organ $organ = null, bool $addRegisterNumbers = true)
    {
        parent::__construct($disposition, $client, $organ, $addRegisterNumbers);
    }

    public function callAddRegisterNumbers(string $disposition): string
    {
        return $this->addRegisterNumbers($disposition);
    }

    public function callGetOrganInfo(): string
    {
        return $this->getOrganInfo();
    }

    public function callSendChatRequest(string $operation, array $payload): string
    {
        return $this->sendChatRequest($operation, $payload);
    }
}


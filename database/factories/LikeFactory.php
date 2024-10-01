<?php

namespace Database\Factories;

use App\Models\OrganBuilder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organ;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganLike>
 */
class LikeFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // náhodné varhany/varhanáři uložení v OrganSeederu/OrganBuilderSeederu
            'likeable_id' => fake()->unique()->numberBetween(1, 8),
            'likeable_type' => fake()->randomElement([Organ::class, OrganBuilder::class]),
            'user_id' => User::factory(),
        ];
    }

}

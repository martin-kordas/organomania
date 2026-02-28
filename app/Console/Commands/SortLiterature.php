<?php

namespace App\Console\Commands;

use Collator;
use Illuminate\Console\Command;
use App\Helpers;
use App\Models\Organ;
use App\Models\OrganBuilder;

class SortLiterature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sort-literature {--type=organ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Alphabetically sort rows in literature column.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $collator = $this->getCollator();

        if ($this->option('type') === 'organ') $model = new Organ;
        else $model = new OrganBuilder;

        // TODO: literatura bez autora začíná znakem '*' (např. Varhaník) a zařadí se proto na začátek
        $items = $model->whereNotNull('literature')->public()->orderBy('id')->get();
        foreach ($items as $item) {
            $literature = Helpers::normalizeLineBreaks($item->literature);
            $rows = explode("\n", $literature);

            $collator->sort($rows);

            $newLiterature = implode("\n", $rows);
            $item->literature = $newLiterature;
            $item->save();

            $this->info("Úspěšně setříděna položka (id: {$item->id})");
        }
    }

    private function getCollator()
    {
        return new Collator('cs_CZ');
    }

}

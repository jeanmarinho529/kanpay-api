<?php

namespace Database\Seeders;

use App\Models\BatchFile\BatchFileType;
use Illuminate\Database\Seeder;

class BatchFileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->data() as $item) {
            BatchFileType::query()->firstOrCreate($item);
        }
    }

    private function data(): array
    {
        return [
            [
                'display_name' => 'Cobranças',
                'name' => 'billing',
            ],
        ];
    }
}

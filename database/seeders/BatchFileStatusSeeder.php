<?php

namespace Database\Seeders;

use App\Models\BatchFile\BatchFileStatus;
use Illuminate\Database\Seeder;

class BatchFileStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->data() as $item) {
            BatchFileStatus::query()->firstOrCreate($item);
        }
    }

    private function data(): array
    {
        return [
            [
                'display_name' => 'Em Progresso',
                'name' => 'progress',
            ],
            [
                'display_name' => 'Concluído',
                'name' => 'done',
            ],
            [
                'display_name' => 'Concluído Parcialmente',
                'name' => 'partial',
            ],
            [
                'display_name' => 'Erro',
                'name' => 'error',
            ],
        ];
    }
}

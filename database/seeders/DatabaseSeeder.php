<?php

namespace Database\Seeders;

use App\Enums\BatchFileStatusEnum;
use App\Models\BatchFile\BatchFile;
use App\Models\BatchFile\BatchFileStatus;
use App\Models\BatchFile\BatchFileType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BatchFileStatusSeeder::class,
            BatchFileTypeSeeder::class,
        ]);

        BatchFile::factory()
            ->hasBatchFileItemError(5)
            ->create([
                'name' => 'file_test-seed.csv',
                'batch_file_type_id' => BatchFileType::first(),
                'batch_file_status_id' => BatchFileStatus::where('name', BatchFileStatusEnum::PARTIAL->value)->first(),
                'total_items' => 10,
                'total_done' => 5,
                'total_failed' => 5,
            ]);
    }
}

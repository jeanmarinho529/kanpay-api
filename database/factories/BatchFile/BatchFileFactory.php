<?php

namespace Database\Factories\BatchFile;

use App\Models\BatchFile\BatchFileStatus;
use App\Models\BatchFile\BatchFileType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BatchFile\BatchFile>
 */
class BatchFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'batch_file_status_id' => BatchFileStatus::factory(),
            'batch_file_type_id' => BatchFileType::factory(),
            'name' => fake()->text(80),
            'path' => fake()->filePath(),
            'total_items' => fake()->randomDigit(),
            'total_done' => fake()->randomDigit(),
            'total_failed' => fake()->randomDigit(),
        ];
    }
}

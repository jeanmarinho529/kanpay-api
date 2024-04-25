<?php

namespace Database\Factories\BatchFile;

use App\Models\BatchFile\BatchFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BatchFile\BatchFileItemError>
 */
class BatchFileItemErrorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'batch_file_id' => BatchFile::factory(),
            'row_number' => fake()->randomDigit(),
            'error' => fake()->title(),
        ];
    }
}

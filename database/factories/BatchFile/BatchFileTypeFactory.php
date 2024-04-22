<?php

namespace Database\Factories\BatchFile;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BatchFile\BatchFileType>
 */
class BatchFileTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'display_name' => fake()->text(80),
            'name' => fake()->unique()->text(50),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tugas>
 */
class TugasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->roleKetuaTingkat(),
            'judul' => fake()->sentence(4),
            'mata_kuliah' => fake()->randomElement(['Basis Data', 'Pemrograman Web', 'Struktur Data', 'Desain UI']),
            'dosen' => fake()->name(),
            'deskripsi' => fake()->paragraph(),
            'tenggat' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
        ];
    }

    /**
     * Tugas yang tenggatnya sudah lewat.
     */
    public function lewatWaktu(): static
    {
        return $this->state(fn (array $attributes) => [
            'tenggat' => fake()->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
        ]);
    }
}

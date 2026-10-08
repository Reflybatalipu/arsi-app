<?php

namespace Database\Factories;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agenda>
 */
class AgendaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul' => fake()->sentence(3),
            'jenis' => fake()->randomElement(['organisasi', 'rencana', 'lainnya']),
            'deskripsi' => fake()->paragraph(),
            'tanggal' => fake()->dateTimeBetween('now', '+60 days')->format('Y-m-d'),
            'waktu' => fake()->randomElement(['08:00', '13:30', '19:00', null]),
        ];
    }
}

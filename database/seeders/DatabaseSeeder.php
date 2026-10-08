<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ketua = User::factory()->roleKetuaTingkat()->create([
            'name' => 'Ketua Tingkat',
            'email' => 'ketua@example.com',
        ]);

        $mahasiswa = User::factory()->roleMahasiswa()->create([
            'name' => 'Mahasiswa Satu',
            'email' => 'mahasiswa@example.com',
        ]);

        User::factory()->count(3)->roleMahasiswa()->create();

        Tugas::factory()->count(3)->create(['user_id' => $ketua->id]);
        Agenda::factory()->count(2)->create(['user_id' => $mahasiswa->id]);
    }
}

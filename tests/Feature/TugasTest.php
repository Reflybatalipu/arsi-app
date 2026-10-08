<?php

use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('seluruh pengguna login dapat melihat daftar tugas', function () {
    $tugas = Tugas::factory()->create();

    $this->actingAs(User::factory()->roleMahasiswa()->create())
        ->get(route('tugas.index'))
        ->assertOk()
        ->assertSee($tugas->judul);
});

test('mahasiswa dapat melihat seluruh tugas yang diinput ketua tingkat', function () {
    Tugas::factory()->count(3)->create();

    $this->actingAs(User::factory()->roleMahasiswa()->create())
        ->get(route('tugas.index'))
        ->assertOk()
        ->assertSee(Tugas::pluck('judul')->all());
});

test('hanya ketua tingkat yang boleh menambah tugas', function () {
    $this->actingAs(User::factory()->roleMahasiswa()->create())
        ->get(route('tugas.create'))
        ->assertForbidden();

    $this->actingAs(User::factory()->roleKetuaTingkat()->create())
        ->get(route('tugas.create'))
        ->assertOk();
});

test('ketua tingkat dapat membuat tugas', function () {
    $ketua = User::factory()->roleKetuaTingkat()->create();

    $response = $this->actingAs($ketua)->post(route('tugas.store'), [
        'judul' => 'Tugas Basis Data Bab 3',
        'mata_kuliah' => 'Basis Data',
        'dosen' => 'Dr. Andi',
        'deskripsi' => 'Kerjakan soal nomor 1 sampai 5.',
        'tenggat' => now()->addWeek()->format('Y-m-d'),
    ]);

    $tugas = Tugas::firstWhere('judul', 'Tugas Basis Data Bab 3');

    expect($tugas)->not->toBeNull()
        ->and($tugas->user_id)->toBe($ketua->id);

    $response->assertRedirect(route('tugas.show', $tugas));
});

test('mahasiswa tidak dapat membuat tugas', function () {
    $response = $this->actingAs(User::factory()->roleMahasiswa()->create())
        ->post(route('tugas.store'), ['judul' => 'Tugas Ilegal']);

    $response->assertForbidden();
    $this->assertDatabaseCount('tugas', 0);
});

test('tugas wajib memiliki judul', function () {
    $this->actingAs(User::factory()->roleKetuaTingkat()->create())
        ->from(route('tugas.create'))
        ->post(route('tugas.store'), ['judul' => ''])
        ->assertSessionHasErrors('judul');
});

test('hanya ketua tingkat pencatat yang dapat mengubah tugasnya', function () {
    $tugas = Tugas::factory()->create();
    $lain = User::factory()->roleKetuaTingkat()->create();

    $this->actingAs($lain)
        ->put(route('tugas.update', $tugas), ['judul' => 'Diubah Lain'])
        ->assertForbidden();

    $this->actingAs(User::factory()->roleMahasiswa()->create())
        ->put(route('tugas.update', $tugas), ['judul' => 'Diubah Mahasiswa'])
        ->assertForbidden();

    $this->actingAs($tugas->user)
        ->put(route('tugas.update', $tugas), ['judul' => 'Diubah Pemilik'])
        ->assertRedirect(route('tugas.show', $tugas));

    expect($tugas->fresh()->judul)->toBe('Diubah Pemilik');
});

test('mahasiswa dapat menandai tugas selesai dan membatalkannya', function () {
    $mahasiswa = User::factory()->roleMahasiswa()->create();
    $tugas = Tugas::factory()->create();

    $this->actingAs($mahasiswa)
        ->post(route('tugas.selesai', $tugas))
        ->assertRedirect();

    $this->assertDatabaseHas('tugas_selesai', [
        'tugas_id' => $tugas->id,
        'user_id' => $mahasiswa->id,
    ]);

    // Toggle kedua kali membatalkan status.
    $this->actingAs($mahasiswa)->post(route('tugas.selesai', $tugas));

    $this->assertDatabaseCount('tugas_selesai', 0);
});

test('penandaan selesai tidak dapat menimpa data orang lain', function () {
    $a = User::factory()->roleMahasiswa()->create();
    $b = User::factory()->roleMahasiswa()->create();
    $tugas = Tugas::factory()->create();

    $this->actingAs($a)->post(route('tugas.selesai', $tugas));
    $this->actingAs($b)->post(route('tugas.selesai', $tugas));

    $this->assertDatabaseCount('tugas_selesai', 2);
});

test('mahasiswa dapat menghapus tugas yang dibuat orang lain', function () {
    $tugas = Tugas::factory()->create();

    $this->actingAs(User::factory()->roleMahasiswa()->create())
        ->delete(route('tugas.destroy', $tugas))
        ->assertForbidden();
});

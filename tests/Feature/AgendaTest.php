<?php

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('agenda yang tampil hanya milik pemiliknya', function () {
    $saya = User::factory()->create();
    $lain = User::factory()->create();

    Agenda::factory()->create(['user_id' => $saya->id, 'judul' => 'Rapat Kordinasi']);
    Agenda::factory()->create(['user_id' => $lain->id, 'judul' => 'Agenda Orang Lain']);

    $this->actingAs($saya)
        ->get(route('agenda.index'))
        ->assertOk()
        ->assertSee('Rapat Kordinasi')
        ->assertDontSee('Agenda Orang Lain');
});

test('pengguna login dapat membuat agenda', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('agenda.store'), [
        'judul' => 'Latihan Organisasi',
        'jenis' => 'organisasi',
        'deskripsi' => 'Latihan gabungan di aula.',
        'tanggal' => now()->addDays(3)->format('Y-m-d'),
        'waktu' => '19:00',
    ]);

    $response->assertRedirect(route('agenda.index'));

    $this->assertDatabaseHas('agendas', [
        'judul' => 'Latihan Organisasi',
        'user_id' => $user->id,
    ]);
});

test('jenis agenda harus salah satu dari pilihan yang diizinkan', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('agenda.create'))
        ->post(route('agenda.store'), [
            'judul' => 'Agenda',
            'jenis' => 'bebas',
            'tanggal' => now()->format('Y-m-d'),
        ])
        ->assertSessionHasErrors('jenis');
});

test('agenda orang lain tidak dapat dilihat', function () {
    $saya = User::factory()->create();
    $agendaLain = Agenda::factory()->create();

    $this->actingAs($saya)
        ->get(route('agenda.show', $agendaLain))
        ->assertForbidden();
});

test('agenda orang lain tidak dapat diubah', function () {
    $saya = User::factory()->create();
    $agendaLain = Agenda::factory()->create();

    $this->actingAs($saya)
        ->put(route('agenda.update', $agendaLain), [
            'judul' => 'Diubah',
            'jenis' => 'rencana',
            'tanggal' => now()->format('Y-m-d'),
        ])
        ->assertForbidden();

    $this->actingAs($saya)
        ->delete(route('agenda.destroy', $agendaLain))
        ->assertForbidden();

    $this->assertDatabaseCount('agendas', 1);
});

test('pemilik agenda dapat mengubah dan menghapus agendanya', function () {
    $user = User::factory()->create();
    $agenda = Agenda::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->put(route('agenda.update', $agenda), [
            'judul' => 'Agenda Baru',
            'jenis' => 'rencana',
            'tanggal' => now()->addDay()->format('Y-m-d'),
            'waktu' => '08:00',
        ])
        ->assertRedirect(route('agenda.index'));

    expect($agenda->fresh()->judul)->toBe('Agenda Baru');

    $this->actingAs($user)
        ->delete(route('agenda.destroy', $agenda))
        ->assertRedirect(route('agenda.index'));

    $this->assertDatabaseCount('agendas', 0);
});

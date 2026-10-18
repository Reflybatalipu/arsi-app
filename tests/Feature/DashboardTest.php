<?php

use App\Models\Agenda;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard menampilkan kartu, pencarian realtime, dan filter', function () {
    $user = User::factory()->roleMahasiswa()->create();

    $tugas = Tugas::factory()->create(['judul' => 'Tugas Basis Data Bab 5']);
    Agenda::factory()->create(['user_id' => $user->id, 'judul' => 'Rapat OSIS Bulanan']);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        // Elemen pencarian realtime.
        ->assertSee('id="search"', false)
        // Kartu item untuk filter client-side.
        ->assertSee('data-item', false)
        ->assertSee('data-filter="jenis"', false)
        ->assertSee('data-filter="status"', false)
        // Konten kartu.
        ->assertSee($tugas->judul)
        ->assertSee('Rapat OSIS Bulanan');
});

test('tugas mendapat peringatan kurang dari 24 jam sebelum tenggat', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Tugas::factory()->create([
        'judul' => 'Tugas Mendekati Tenggat',
        'tenggat' => today()->format('Y-m-d'), // tenggat akhir hari ini => kurang dari 24 jam
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Tugas mendekati tenggat: Tugas Mendekati Tenggat');
});

test('tugas yang masih jauh dari tenggat tidak memunculkan peringatan', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Tugas::factory()->create([
        'judul' => 'Tugas Masih Jauh',
        'tenggat' => today()->addDays(3)->format('Y-m-d'),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Tugas mendekati tenggat: Tugas Masih Jauh')
        ->assertSee('Tugas Masih Jauh'); // tetap tampil sebagai kartu
});

test('tugas yang sudah diselesaikan tidak memunculkan peringatan', function () {
    $user = User::factory()->roleMahasiswa()->create();
    $tugas = Tugas::factory()->create([
        'judul' => 'Tugas Sudah Selesai',
        'tenggat' => today()->format('Y-m-d'),
    ]);

    $user->tugasSelesai()->attach($tugas->id);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Tugas mendekati tenggat: Tugas Sudah Selesai');
});

test('tugas yang tenggatnya terlampaui tetap memberi peringatan', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Tugas::factory()->create([
        'judul' => 'Tugas Terlambat',
        'tenggat' => today()->subDay()->format('Y-m-d'),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Tenggat terlampaui')
        ->assertSee('Tugas Terlambat');
});

test('agenda mendapat peringatan 5 jam sebelum waktu pelaksanaan', function () {
    $user = User::factory()->roleMahasiswa()->create();

    $dekat = now()->addHours(2);
    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Agenda Segera Dimulai',
        'tanggal' => $dekat->format('Y-m-d'),
        'waktu' => $dekat->format('H:i'),
    ]);

    $jauh = now()->addHours(10);
    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Agenda Masih Jauh',
        'tanggal' => $jauh->format('Y-m-d'),
        'waktu' => $jauh->format('H:i'),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Agenda segera dimulai: Agenda Segera Dimulai')
        ->assertDontSee('Agenda segera dimulai: Agenda Masih Jauh');
});

test('agenda orang lain tidak muncul di dashboard', function () {
    $saya = User::factory()->roleMahasiswa()->create();
    $lain = User::factory()->roleMahasiswa()->create();

    Agenda::factory()->create(['user_id' => $lain->id, 'judul' => 'Agenda Rahasia Orang Lain']);

    $this->actingAs($saya)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Agenda Rahasia Orang Lain');
});

test('tugas mendekati tenggat menampilkan prioritas kritis', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Tugas::factory()->create([
        'judul' => 'Tugas Kritis',
        'tenggat' => today()->format('Y-m-d'),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Prioritas Kritis');
});

test('pada tenggat yang sama, tugas didahulukan atas agenda di rekomendasi', function () {
    $user = User::factory()->roleMahasiswa()->create();

    // Keduanya berdeadline hari ini pukul 23:59:59.
    Tugas::factory()->create([
        'judul' => 'Tugas Duluanku',
        'tenggat' => today()->format('Y-m-d'),
    ]);

    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Agenda Kembar Waktunya',
        'tanggal' => today()->format('Y-m-d'),
        'waktu' => '23:59:59',
    ]);

    $html = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

    $posTugas = strpos($html, 'Tugas Duluanku');
    $posAgenda = strpos($html, 'Agenda Kembar Waktunya');

    expect($posTugas)->not->toBeFalse()
        ->and($posAgenda)->not->toBeFalse()
        ->and($posTugas)->toBeLessThan($posAgenda);
});

test('rekomendasi pengerjaan diurutkan dari tenggat terdekat', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Tugas::factory()->create([
        'judul' => 'Tugas Jauh Nanti',
        'tenggat' => today()->addDays(20)->format('Y-m-d'),
    ]);

    Tugas::factory()->create([
        'judul' => 'Tugas Segera Mendingin',
        'tenggat' => today()->format('Y-m-d'),
    ]);

    $html = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

    $posDekat = strpos($html, 'Tugas Segera Mendingin');
    $posJauh = strpos($html, 'Tugas Jauh Nanti');

    expect($posDekat)->not->toBeFalse()
        ->and($posJauh)->not->toBeFalse()
        ->and($posDekat)->toBeLessThan($posJauh);
});

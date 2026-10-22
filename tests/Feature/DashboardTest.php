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

test('agenda yang waktu pelaksanaannya sudah lewat otomatis berstatus selesai', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Agenda Lampau Otomatis',
        'tanggal' => now()->subDay()->format('Y-m-d'),
        'waktu' => '08:00',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Agenda Lampau Otomatis')
        ->assertSee('data-status="selesai"', false)
        ->assertDontSee('data-status="lewat"', false);
});

test('agenda yang baru selesai otomatis menampilkan notifikasi selama 24 jam', function () {
    $user = User::factory()->roleMahasiswa()->create();

    $baruSaja = now()->subHours(20);
    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Minum Kopi Bersama',
        'tanggal' => $baruSaja->format('Y-m-d'),
        'waktu' => $baruSaja->format('H:i'),
    ]);

    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Agenda Lama Sekali',
        'tanggal' => now()->subDays(5)->format('Y-m-d'),
        'waktu' => '08:00',
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Agenda selesai: Minum Kopi Bersama')
        ->assertDontSee('Agenda selesai: Agenda Lama Sekali')
        ->assertSee('Otomatis ditandai selesai setelah waktu pelaksanaan.');
});

test('agenda yang sudah selesai otomatis tidak masuk rekomendasi pengerjaan', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Agenda Lampau Tak Direkomendasikan',
        'tanggal' => now()->subDay()->format('Y-m-d'),
        'waktu' => '09:00',
    ]);

    $html = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

    // Ambil hanya bagian panel rekomendasi (antara judul section dan toolbar pencarian).
    $mulai = strpos($html, 'Rekomendasi Pengerjaan');
    $akhir = strpos($html, 'id="search"');
    $rekomendasi = substr($html, $mulai, $akhir - $mulai);

    expect($rekomendasi)->not->toContain('Agenda Lampau Tak Direkomendasikan')
        // Tetap tampil sebagai kartu di daftar lengkap.
        ->and(substr($html, $akhir))->toContain('Agenda Lampau Tak Direkomendasikan');
});

test('dashboard merender nav di atas konten di dalam container main', function () {
    $user = User::factory()->roleMahasiswa()->create();

    $html = $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->getContent();

    $posNav = strpos($html, '<nav');
    $posMain = strpos($html, '<main');
    $posHalo = strpos($html, 'Halo,');

    expect($posNav)->not->toBeFalse()
        ->and($posMain)->not->toBeFalse()
        ->and($posHalo)->not->toBeFalse()
        // Nav tampil sebelum konten, konten tampil setelah <main> container.
        ->and($posNav)->toBeLessThan($posMain)
        ->and($posMain)->toBeLessThan($posHalo)
        // Tidak ada output konten yang bocor sebelum kerangka layout.
        ->and(strpos($html, '<!DOCTYPE'))->toBe(0);
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

test('agenda yang baru lewat waktu tidak menampilkan label telat', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Agenda Baru Lewat',
        'tanggal' => now()->subHours(3)->format('Y-m-d'),
        'waktu' => now()->subHours(3)->format('H:i'),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Telat 3 jam')
        ->assertSee('Sudah selesai');
});

test('agenda yang lewat waktu tidak disematkan di rekomendasi pengerjaan', function () {
    $user = User::factory()->roleMahasiswa()->create();

    Agenda::factory()->create([
        'user_id' => $user->id,
        'judul' => 'Agenda Lewat Tak Direkomendasikan',
        'tanggal' => now()->subHours(2)->format('Y-m-d'),
        'waktu' => now()->subHours(2)->format('H:i'),
    ]);

    $html = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

    $mulai = strpos($html, 'Rekomendasi Pengerjaan');
    $akhir = strpos($html, 'id="search"');
    $rekomendasi = substr($html, $mulai, $akhir - $mulai);

    expect($rekomendasi)->not->toContain('Agenda Lewat Tak Direkomendasikan');
});

test('popup ringkasan prioritas tampil setelah login dan hilang saat dikunjungi lagi', function () {
    $user = User::factory()->roleMahasiswa()->create();

    $this->post(route('login.attempt'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('id="popup-peringatan"', false)
        ->assertSee('Ringkasan Prioritas');

    // Kunjungan berikutnya: popup tidak muncul lagi.
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('id="popup-peringatan"', false);
});

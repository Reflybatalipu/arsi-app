<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('pengguna dapat mendaftar dengan memilih peran', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'ketua_tingkat',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $this->assertDatabaseHas('users', [
        'email' => 'budi@example.com',
        'role' => Role::KetuaTingkat->value,
    ]);
});

test('registrasi menolak peran di luar pilihan yang diizinkan', function () {
    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Hacker',
        'email' => 'hacker@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'admin',
    ]);

    $response->assertSessionHasErrors('role');
    $this->assertGuest();
});

test('pengguna dapat login dengan kredensial yang benar', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $response = $this->post(route('login.attempt'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('login gagal dengan password salah', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $response = $this->post(route('login.attempt'), [
        'email' => $user->email,
        'password' => 'salah',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('pengguna dapat logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('route terproteksi mengarahkan tamu ke halaman login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('tugas.index'))->assertRedirect(route('login'));
    $this->get(route('agenda.index'))->assertRedirect(route('login'));
});

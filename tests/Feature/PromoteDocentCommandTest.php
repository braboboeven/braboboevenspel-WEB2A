<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('promotes an existing user to docent', function () {
    $user = User::factory()->create(['is_docent' => false]);

    $this->artisan('docent:promote', ['email' => $user->email])
        ->assertExitCode(0);

    expect($user->fresh()->is_docent)->toBeTrue();
});

it('blocks promotion when a docent already exists', function () {
    User::factory()->create(['is_docent' => true]);
    $user = User::factory()->create(['is_docent' => false]);

    $this->artisan('docent:promote', ['email' => $user->email])
        ->assertExitCode(1);

    expect($user->fresh()->is_docent)->toBeFalse();
});

it('creates and promotes a user when requested', function () {
    $email = 'docent@example.com';
    $password = 'secret-password';

    $this->artisan('docent:promote', [
        'email' => $email,
        '--create' => true,
        '--name' => 'Docent User',
        '--password' => $password,
    ])->assertExitCode(0);

    $user = User::query()->where('email', $email)->first();

    expect($user)->not->toBeNull();
    expect($user->is_docent)->toBeTrue();
    expect(Hash::check($password, $user->password))->toBeTrue();
});

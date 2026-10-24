<?php

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Support\Facades\Hash;

it('creates the default admins and keeps an existing password', function () {
    $this->seed(AdminSeeder::class);

    $admin = Admin::query()->where('email', 'super_admin@domain.com')->first();

    expect($admin)->not->toBeNull()
        ->and(Hash::check('password', $admin->password))->toBeTrue()
        ->and($admin->hasRole('super_admin'))->toBeTrue();

    $admin->update([
        'password' => 'custom-password',
    ]);

    $this->seed(AdminSeeder::class);

    expect(Hash::check('custom-password', $admin->fresh()->password))->toBeTrue();
});

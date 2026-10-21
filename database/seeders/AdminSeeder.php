<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Developer',
                'email' => 'developer@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Super Admin',
                'email' => 'super_admin@domain.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        ];

        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'admin'],
        );

        foreach ($admins as $data) {
            $admin = Admin::query()->updateOrCreate(
                ['email' => $data['email']],
                $data,
            );

            if (! $admin->hasRole($superAdminRole)) {
                $admin->assignRole($superAdminRole);
            }
        }
    }
}

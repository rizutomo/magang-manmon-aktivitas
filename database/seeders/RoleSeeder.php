<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Sesuaikan dengan model User Anda
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Membuat Role
        $adminRole = Role::create(['uuid' => '99441bfe-045a-48b9-b051-95bc28e17c7d', 'name' => 'admin', 'guard_name' => 'api']);
        $supervisorRole = Role::create(['uuid' => 'e2b92e6a-acd9-4805-b8ac-5cdacf0a3433', 'name' => 'supervisor', 'guard_name' => 'api']);
        $userRole = Role::create(['uuid' => '939e3c6a-3b83-4316-918a-c85a86daeeb7', 'name' => 'user', 'guard_name' => 'api']);

        // Mendapatkan User (pastikan user sudah ada di database)
        $admins = User::where('email', 'like', '%admin%')->get();
        $users = User::where('email', 'like', '%user%')->get();
        $supervisors = User::where('email', 'like', '%supervisor%')->get();

        // Assign role 'admin' ke semua admin
        foreach ($admins as $admin) {
            $admin->assignRole('admin');
        }

        // Assign role 'supervisor' ke semua supervisor biasa
        foreach ($supervisors as $supervisor) {
            $supervisor->assignRole('supervisor');
        }

        // Assign role 'user' ke semua user biasa
        foreach ($users as $user) {
            $user->assignRole('user');
        }
    }
}

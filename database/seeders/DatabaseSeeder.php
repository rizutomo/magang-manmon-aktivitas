<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Supervisor;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'User1',
            'email' => 'user1@example.com',
        ]);
        User::factory()->create([
            'name' => 'User2',
            'email' => 'user2@example.com',
        ]);
        User::factory()->create([
            'name' => 'User3',
            'email' => 'user3@example.com',
        ]);
        Admin::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        Supervisor::factory()->create([
            'name' => 'Supervisor',
            'email' => 'supervisor@example.com',
        ]);
    }
}

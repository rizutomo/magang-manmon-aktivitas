<?php

namespace Database\Seeders;

use App\Models\Occupation;
use App\Models\Program;
use App\Models\Sector;
use App\Models\Task;
use App\Models\User;
use App\Models\Report;
use App\Models\Admin;
use App\Models\Supervisor;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // BIDANG
        $sector1 = Sector::create([
            'id' => Str::uuid(),
            'name' => 'Tata Kelola Informatika'
        ]);
        $sector2 = Sector::create([
            'id' => Str::uuid(),
            'name' => 'Informasi dan Komunikasi Publik'
        ]);
        $sector3 = Sector::create([
            'id' => Str::uuid(),
            'name' => 'Kesekretariatan'
        ]);

        // JABATAN
        $occupation1 = Occupation::create([
            'id' => Str::uuid(),
            'name' => 'System Analyst',
            'sector_id' => $sector1->id,
        ]);
        $occupation2 = Occupation::create([
            'id' => Str::uuid(),
            'name' => 'Programmer',
            'sector_id' => $sector1->id,
        ]);
        $occupation3 = Occupation::create([
            'id' => Str::uuid(),
            'name' => 'Kepala Dinas',
            'sector_id' => $sector3->id,
        ]);
        $occupation4 = Occupation::create([
            'id' => Str::uuid(),
            'name' => 'Kepala Bidang Tata Kelola Informatika',
            'sector_id' => $sector1->id,
        ]);
        $occupation5 = Occupation::create([
            'id' => Str::uuid(),
            'name' => 'Kepala Bidang Informasi dan Komunikasi Publik',
            'sector_id' => $sector2->id,
        ]);
        $occupation6 = Occupation::create([
            'id' => Str::uuid(),
            'name' => 'Pengelolaan Media dan Data Statistik',
            'sector_id' => $sector2->id,
        ]);
        $occupation7 = Occupation::create([
            'id' => Str::uuid(),
            'name' => 'Pelayanan Informasi Publik',
            'sector_id' => $sector2->id,
        ]);

        // ACCOUNT
        $user1 = User::factory()->create([
            'occupation_id' => $occupation1->id,
            'name' => 'User1',
            'email' => 'user1@example.com',
            'id' => 'b53ad881-14f5-498e-a9cb-9354f9b69ff1',
        ]);
        $user2 = User::factory()->create([
            'occupation_id' => $occupation2->id,
            'name' => 'User2',
            'email' => 'user2@example.com',
        ]);
        $user3 = User::factory()->create([
            'occupation_id' => $occupation2->id,
            'name' => 'User3',
            'email' => 'user3@example.com',
        ]);
        $user4 = User::factory()->create([
            'occupation_id' => $occupation2->id,
            'name' => 'User4',
            'email' => 'user4@example.com',
        ]);
        $user5 = User::factory()->create([
            'occupation_id' => $occupation6->id,
            'name' => 'User5',
            'email' => 'user5@example.com',
        ]);
        $user6 = User::factory()->create([
            'occupation_id' => $occupation7->id,
            'name' => 'User6',
            'email' => 'user6@example.com',
        ]);
        $admin = User::factory()->create([
            'occupation_id' => $occupation3->id,
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $supervisor1 = User::factory()->create([
            'occupation_id' => $occupation4->id,
            'name' => 'Supervisor1',
            'sector_id' => $sector1->id,
            'email' => 'supervisor1@example.com',
        ]);
        $supervisor2 = User::factory()->create([
            'occupation_id' => $occupation5->id,
            'name' => 'Supervisor2',
            'sector_id' => $sector2->id,
            'email' => 'supervisor2@example.com',
        ]);

        // Program
        $program1 = Program::create([
            'id' => Str::uuid(),
            'sector_id' => $sector1->id,
            'name' => 'Perbaikan CCTV',
            'description' => 'Perbaikan CCTV di kantor Diskominfo Karanganyar',
            'start_date' => '2024-9-1',
            'end_date' => '2024-9-15',
        ]);
        $program2 = Program::create([
            'id' => Str::uuid(),
            'sector_id' => $sector1->id,
            'name' => 'Perbaikan Router Wi-Fi',
            'description' => 'Perbaikan router Wi-Fi di kantor Diskominfo Karanganyar',
            'start_date' => '2024-9-1',
            'end_date' => '2024-9-15',
        ]);

        $program3 = Program::create([
            'id' => Str::uuid(),
            'sector_id' => $sector1->id,
            'name' => 'Perbaikan Kabel Ethernet',
            'description' => 'Perbaikan kabel ethernet di kantor Diskominfo Karanganyar',
            'start_date' => '2024-9-1',
            'end_date' => '2024-12-22',
        ]);

        // TEAM
        $user1->programs()->attach($program1->id, ['role' => 'ketua']);
        $user2->programs()->attach($program1->id, ['role' => 'anggota']);
        $user3->programs()->attach($program1->id, ['role' => 'anggota']);
        $user4->programs()->attach($program1->id, ['role' => 'anggota']);

        // KEGIATAN
        $task1 = Task::create([
            'program_id' => $program1->id,
            'id' => Str::uuid(),
            'name' => 'Perbaikan CCTV Gedung A',
            'host' => 'Diskominfo Karanganyar',
            'date' => '2024-9-1',
            'location' => 'Karanganyar',
            'time' => '08:30:00',
            'description' => 'Memperbaiki CCTV di lantai 1',
            'file' => 'task_file.pdf',
        ]);
        $task2 = Task::create([
            'program_id' => $program1->id,
            'id' => Str::uuid(),
            'name' => 'Perbaikan CCTV Gedung B',
            'host' => 'Diskominfo Karanganyar',
            'date' => '2024-9-2',
            'location' => 'Karanganyar',
            'time' => '08:30:00',
            'description' => 'Memperbaiki CCTV di lantai 2',
            'file' => 'task_file.pdf',
        ]);
        $task3 = Task::create([
            'program_id' => $program2->id,
            'id' => Str::uuid(),
            'name' => 'Perbaikan Router Gedung C',
            'host' => 'Diskominfo Karanganyar',
            'date' => '2024-12-20',
            'location' => 'Karanganyar',
            'time' => '08:30:00',
            'description' => 'Memperbaiki CCTV di lantai 2 lagi',
            'file' => 'task_file.pdf',
        ]);

        $Report1 = Report::create([
            'id' => Str::uuid(),
            'task_id' => $task1->id,
            'description' => 'CCTV berhasil diperbaiki',
            'longitude' => '-7.595910857727733',
            'latitude' => '110.94004966685802',
            'date' => '2024-9-1',
            'modified_by' => $user1->id,
            'comment' => 'nice'
        ]);
        $Report2 = Report::create([
            'id' => Str::uuid(),
            'task_id' => $task3->id,
            'description' => 'Router Wi-Fi berhasil diperbaiki',
            'longitude' => '-7.595910857727733',
            'latitude' => '110.94004966685802',
            'date' => '2024-9-2',
            'modified_by' => $user3->id,
            'comment' => 'nice'
        ]);

        $user1->tasks()->attach($task1->id);
        $user2->tasks()->attach($task1->id);
        $user3->tasks()->attach($task1->id);
        $user4->tasks()->attach($task1->id);
        
        $user1->tasks()->attach($task2->id);
        $user3->tasks()->attach($task2->id);
        
        $user3->tasks()->attach($task3->id);
        $user4->tasks()->attach($task3->id);
    }
}

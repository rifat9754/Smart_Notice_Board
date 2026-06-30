<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Notice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Department Admin',
            'email' => 'admin@dept.edu',
            'password' => 'password',
            'role' => 'super_admin',
            'department' => 'CSE',
        ]);

        $teacher = User::create([
            'name' => 'Demo Teacher',
            'email' => 'teacher@dept.edu',
            'password' => 'password',
            'role' => 'teacher',
            'department' => 'CSE',
        ]);

        User::create([
            'name' => 'Demo Student',
            'email' => 'student@dept.edu',
            'password' => 'password',
            'role' => 'student',
            'department' => 'CSE',
        ]);

        Notice::create([
            'title' => 'Welcome to the Digital Notice Board',
            'body' => 'This is a sample high-priority notice.',
            'type' => 'text',
            'priority' => 'high',
            'status' => 'published',
            'author_id' => $admin->id,
        ]);

        Notice::create([
            'title' => 'Mid-term Exam Routine Published',
            'body' => 'Mid-term exams start next week. Check the routine.',
            'type' => 'text',
            'priority' => 'medium',
            'status' => 'published',
            'author_id' => $teacher->id,
        ]);
    }
}

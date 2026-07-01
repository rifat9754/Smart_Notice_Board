<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@dept.edu'],
            [
                'name'       => 'Department Admin',
                'password'   => Hash::make('password'),
                'role'       => 'super_admin',
                'department' => 'CSE',
                'status'     => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@dept.edu'],
            [
                'name'       => 'Demo Teacher',
                'password'   => Hash::make('password'),
                'role'       => 'teacher',
                'department' => 'CSE',
                'status'     => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'student@dept.edu'],
            [
                'name'       => 'Demo Student',
                'password'   => Hash::make('password'),
                'role'       => 'student',
                'department' => 'CSE',
                'status'     => 'active',
            ]
        );
    }
}
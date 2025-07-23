<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Assessor User',
            'email' => 'assessor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'assessor',
        ]);

        User::create([
            'name' => 'Consultant User',
            'email' => 'consultant@example.com',
            'password' => Hash::make('password123'),
            'role' => 'consultant',
        ]);

    }
}
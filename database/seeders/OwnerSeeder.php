<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('owner0987'),
            'role' => 'owner',
        ]);
    }
}

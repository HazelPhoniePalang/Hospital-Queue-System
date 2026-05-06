<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Palang Hospital Admin',
            'email' => 'admin@xyzhospital.com',
            'password' => Hash::make('admin123'),
            'role_id' => 3, // Administrator role
        ]);
    }

    
}

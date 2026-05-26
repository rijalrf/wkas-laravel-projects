<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@wkas.com'],
            [
                'name' => 'Admin MKAS',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'photo' => null,
            ]
        );
    }
}

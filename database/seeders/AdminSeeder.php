<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bpkb.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'opd_id' => null,
                'aktif' => true,
            ]
        );
    }
}

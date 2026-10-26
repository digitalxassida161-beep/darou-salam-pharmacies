<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@darousalamsante.local'],
            [
                'name' => 'Administrateur',
                'password' => 'ChangeMoi123!',
                'role' => 'admin',
            ]
        );
    }
}

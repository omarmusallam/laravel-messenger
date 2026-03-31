<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Omar Musallam',   'email' => 'omar@sannad-demo.test'],
            ['name' => 'Sara Ali',        'email' => 'sara@sannad-demo.test'],
            ['name' => 'Ahmad Khaled',    'email' => 'ahmad@sannad-demo.test'],
            ['name' => 'Noor Hasan',      'email' => 'noor@sannad-demo.test'],
            ['name' => 'Lina Samir',      'email' => 'lina@sannad-demo.test'],
            ['name' => 'Support Team',    'email' => 'support@sannad-demo.test'],
            ['name' => 'Project Manager', 'email' => 'manager@sannad-demo.test'],
            ['name' => 'Design Lead',     'email' => 'design@sannad-demo.test'],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

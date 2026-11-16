<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevelopmentUserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun pengujian hanya boleh dibuat di environment local.
        if (!app()->environment('local')) {
            return;
        }

        $users = [
            [
                'name' => 'Secretary User',
                'email' => 'secretary@approseal.test',
                'role' => 'secretary',
            ],
            [
                'name' => 'Approver User',
                'email' => 'approver@approseal.test',
                'role' => 'approver',
            ],
            [
                'name' => 'Administrator User',
                'email' => 'admin@approseal.test',
                'role' => 'administrator',
            ],
            [
                'name' => 'Verifier User',
                'email' => 'verifier@approseal.test',
                'role' => 'verifier',
            ],
        ];

        foreach ($users as $data) {

            $role = Role::where('slug', $data['role'])
                ->firstOrFail();

            User::updateOrCreate(
                [
                    'email' => $data['email'],
                ],
                [
                    'name' => $data['name'],
                    'role_id' => $role->id,
                    'password' => Hash::make('ApproSeal@123'),
                ]
            );
        }
    }
}

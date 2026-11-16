<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's roles.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Secretary',
                'slug' => 'secretary',
                'description' => 'Mengelola dokumen, mengajukan dokumen untuk persetujuan, memantau proses approval, dan mempublikasikan dokumen.',
                'is_system' => true,
            ],
            [
                'name' => 'Approver',
                'slug' => 'approver',
                'description' => 'Meninjau dokumen dan memberikan keputusan persetujuan atau penolakan.',
                'is_system' => true,
            ],
            [
                'name' => 'Administrator',
                'slug' => 'administrator',
                'description' => 'Mengelola pengguna, role, wallet, jenis dokumen, approval policy, dan audit log.',
                'is_system' => true,
            ],
            [
                'name' => 'Verifier',
                'slug' => 'verifier',
                'description' => 'Memeriksa keaslian, integritas, versi, dan status publikasi dokumen.',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                array_merge($role, [
                    'updated_at' => now(),
                ])
            );

            DB::table('roles')
                ->where('slug', $role['slug'])
                ->whereNull('created_at')
                ->update(['created_at' => now()]);
        }
    }
}

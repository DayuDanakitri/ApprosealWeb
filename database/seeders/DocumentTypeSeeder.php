<?php

namespace Database\Seeders;

use App\Models\ApprovalPolicy;
use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $financialPolicy = ApprovalPolicy::where(
            'code',
            'FINANCIAL_APPROVAL'
        )->firstOrFail();

        $standardPolicy = ApprovalPolicy::where(
            'code',
            'STANDARD_APPROVAL'
        )->firstOrFail();

        $documentTypes = [
            [
                'name' => 'Proposal',
                'code' => 'PROP',
                'description' => 'Proposal kegiatan, termasuk RAB sebagai lampiran.',
                'approval_policy_id' => $financialPolicy->id,
            ],
            [
                'name' => 'Surat Keputusan',
                'code' => 'SK',
                'description' => 'Dokumen surat keputusan.',
                'approval_policy_id' => $standardPolicy->id,
            ],
            [
                'name' => 'Surat Permohonan',
                'code' => 'SP',
                'description' => 'Dokumen surat permohonan.',
                'approval_policy_id' => $standardPolicy->id,
            ],
            [
                'name' => 'Surat Tugas',
                'code' => 'ST',
                'description' => 'Dokumen surat tugas.',
                'approval_policy_id' => $standardPolicy->id,
            ],
            [
                'name' => 'Berita Acara',
                'code' => 'BA',
                'description' => 'Dokumen berita acara.',
                'approval_policy_id' => $standardPolicy->id,
            ],
            [
                'name' => 'Laporan Kegiatan',
                'code' => 'LK',
                'description' => 'Dokumen laporan kegiatan.',
                'approval_policy_id' => $standardPolicy->id,
            ],
            [
                'name' => 'Laporan Pertanggungjawaban',
                'code' => 'LPJ',
                'description' => 'Laporan pertanggungjawaban kegiatan dan keuangan.',
                'approval_policy_id' => $financialPolicy->id,
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::updateOrCreate(
                ['code' => $type['code']],
                array_merge($type, [
                    'is_active' => true,
                ])
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\ApprovalPolicy;
use App\Models\ApprovalStep;
use Illuminate\Database\Seeder;

class ApprovalPolicySeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Policy 1: Financial Document Approval
        |--------------------------------------------------------------------------
        */

        $financialPolicy = ApprovalPolicy::updateOrCreate(
            ['code' => 'FINANCIAL_APPROVAL'],
            [
                'name' => 'Financial Document Approval',
                'description' => 'Approval untuk proposal dengan RAB dan laporan pertanggungjawaban keuangan.',
                'is_active' => true,
            ]
        );

        $financialSteps = [
            [
                'step_order' => 1,
                'name' => 'Bendahara Approval',
                'description' => 'Memeriksa RAB dan aspek keuangan dokumen.',
            ],
            [
                'step_order' => 2,
                'name' => 'Chairperson Approval',
                'description' => 'Memeriksa dan menyetujui dokumen sebagai Ketua.',
            ],
            [
                'step_order' => 3,
                'name' => 'Advisor Approval',
                'description' => 'Memberikan persetujuan akhir sebagai Pembina.',
            ],
        ];

        foreach ($financialSteps as $step) {
            ApprovalStep::updateOrCreate(
                [
                    'approval_policy_id' => $financialPolicy->id,
                    'step_order' => $step['step_order'],
                ],
                [
                    'name' => $step['name'],
                    'description' => $step['description'],
                    'is_required' => true,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Policy 2: Standard Document Approval
        |--------------------------------------------------------------------------
        */

        $standardPolicy = ApprovalPolicy::updateOrCreate(
            ['code' => 'STANDARD_APPROVAL'],
            [
                'name' => 'Standard Document Approval',
                'description' => 'Approval untuk surat dan dokumen organisasi nonkeuangan.',
                'is_active' => true,
            ]
        );

        $standardSteps = [
            [
                'step_order' => 1,
                'name' => 'Chairperson Approval',
                'description' => 'Memeriksa dan menyetujui dokumen sebagai Ketua.',
            ],
            [
                'step_order' => 2,
                'name' => 'Advisor Approval',
                'description' => 'Memberikan persetujuan akhir sebagai Pembina.',
            ],
        ];

        foreach ($standardSteps as $step) {
            ApprovalStep::updateOrCreate(
                [
                    'approval_policy_id' => $standardPolicy->id,
                    'step_order' => $step['step_order'],
                ],
                [
                    'name' => $step['name'],
                    'description' => $step['description'],
                    'is_required' => true,
                ]
            );
        }
    }
}

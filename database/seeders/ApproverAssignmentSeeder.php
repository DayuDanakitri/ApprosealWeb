<?php

namespace Database\Seeders;

use App\Models\ApprovalPolicy;
use App\Models\ApprovalStepAssignment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ApproverAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder ini hanya digunakan untuk pengembangan lokal.
        if (!app()->environment('local')) {
            return;
        }

        $approverRole = Role::where('slug', 'approver')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Create Approver Accounts
        |--------------------------------------------------------------------------
        */

        $approvers = [
            'treasurer' => [
                'name' => 'Bendahara',
                'email' => 'bendahara@approseal.test',
            ],
            'chairperson' => [
                'name' => 'Ketua',
                'email' => 'ketua@approseal.test',
            ],
            'advisor' => [
                'name' => 'Pembina',
                'email' => 'pembina@approseal.test',
            ],
        ];

        $users = [];

        foreach ($approvers as $key => $data) {
            $users[$key] = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role_id' => $approverRole->id,
                    'password' => Hash::make('ApproSeal@123'),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Financial Document Approval Assignments
        |--------------------------------------------------------------------------
        */

        $financialPolicy = ApprovalPolicy::where(
            'code',
            'FINANCIAL_APPROVAL'
        )->firstOrFail();

        $financialSteps = $financialPolicy->steps()
            ->get()
            ->keyBy('step_order');

        $financialAssignments = [
            1 => $users['treasurer']->id,
            2 => $users['chairperson']->id,
            3 => $users['advisor']->id,
        ];

        foreach ($financialAssignments as $stepOrder => $userId) {
            $step = $financialSteps->get($stepOrder);

            if (!$step) {
                throw new \RuntimeException(
                    "Financial approval step {$stepOrder} tidak ditemukan."
                );
            }

            ApprovalStepAssignment::updateOrCreate(
                [
                    'approval_step_id' => $step->id,
                    'user_id' => $userId,
                ],
                []
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Standard Document Approval Assignments
        |--------------------------------------------------------------------------
        */

        $standardPolicy = ApprovalPolicy::where(
            'code',
            'STANDARD_APPROVAL'
        )->firstOrFail();

        $standardSteps = $standardPolicy->steps()
            ->get()
            ->keyBy('step_order');

        $standardAssignments = [
            1 => $users['chairperson']->id,
            2 => $users['advisor']->id,
        ];

        foreach ($standardAssignments as $stepOrder => $userId) {
            $step = $standardSteps->get($stepOrder);

            if (!$step) {
                throw new \RuntimeException(
                    "Standard approval step {$stepOrder} tidak ditemukan."
                );
            }

            ApprovalStepAssignment::updateOrCreate(
                [
                    'approval_step_id' => $step->id,
                    'user_id' => $userId,
                ],
                []
            );
        }
    }
}

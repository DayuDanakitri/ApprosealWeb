
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_version_id')
                ->constrained('document_versions')
                ->restrictOnDelete();

            $table->foreignId('approval_step_id')
                ->constrained('approval_steps')
                ->restrictOnDelete();

            $table->foreignId('approver_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('decision', 20)->nullable();

            $table->text('comments')->nullable();

            $table->timestamp('decided_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['document_version_id', 'approval_step_id', 'approver_id'],
                'document_approval_assignment_unique'
            );

            $table->index(['approver_id', 'decision']);
            $table->index(['document_version_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_approvals');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_step_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('approval_step_id')
                ->constrained('approval_steps')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(
                ['approval_step_id', 'user_id'],
                'approval_step_user_unique'
            );

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_step_assignments');
    }
};

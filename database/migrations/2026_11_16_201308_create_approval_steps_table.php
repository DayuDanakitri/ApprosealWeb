
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('approval_policy_id')
                ->constrained('approval_policies')
                ->cascadeOnDelete();

            $table->unsignedInteger('step_order');

            $table->string('name', 150);

            $table->text('description')->nullable();

            $table->boolean('is_required')->default(true);

            $table->timestamps();

            $table->unique(
                ['approval_policy_id', 'step_order'],
                'approval_policy_step_order_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};

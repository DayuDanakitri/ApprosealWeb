
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_type_id')
                ->constrained('document_types')
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('approval_policy_id')
                ->nullable();

            $table->string('onchain_document_id', 78)
                ->nullable()
                ->unique();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('reference_number', 100)->nullable()->unique();

            $table->unsignedInteger('current_version')->default(1);

            $table->string('application_status', 50)
                ->default('draft');

            $table->string('blockchain_status', 50)
                ->nullable();

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index(['created_by', 'application_status']);
            $table->index(['document_type_id', 'application_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

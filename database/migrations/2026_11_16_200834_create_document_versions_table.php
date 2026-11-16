
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();

            $table->unsignedInteger('version_number');

            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');

            $table->char('document_hash', 64);
            $table->char('blockchain_hash', 66)->nullable();

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('blockchain_status', 50)
                ->nullable();

            $table->timestamp('registered_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['document_id', 'version_number'],
                'document_version_unique'
            );

            $table->index(['document_id', 'blockchain_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};

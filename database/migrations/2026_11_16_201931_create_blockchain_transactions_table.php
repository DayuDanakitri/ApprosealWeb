
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blockchain_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_id')
                ->nullable()
                ->constrained('documents')
                ->restrictOnDelete();

            $table->foreignId('document_version_id')
                ->nullable()
                ->constrained('document_versions')
                ->restrictOnDelete();

            $table->foreignId('initiated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('transaction_hash', 66)
                ->nullable()
                ->unique();

            $table->string('transaction_type', 50);

            $table->string('wallet_address', 42)->nullable();

            $table->unsignedBigInteger('chain_id')->nullable();

            $table->string('status', 30)->default('pending');

            $table->unsignedBigInteger('block_number')->nullable();

            $table->string('block_hash', 66)->nullable();

            $table->text('error_message')->nullable();

            $table->timestamp('submitted_at')->nullable();

            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();

            $table->index(['document_id', 'transaction_type']);
            $table->index(['document_version_id', 'status']);
            $table->index(['initiated_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blockchain_transactions');
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blockchain_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blockchain_transaction_id')
                ->nullable()
                ->constrained('blockchain_transactions')
                ->nullOnDelete();

            $table->foreignId('document_id')
                ->nullable()
                ->constrained('documents')
                ->nullOnDelete();

            $table->foreignId('document_version_id')
                ->nullable()
                ->constrained('document_versions')
                ->nullOnDelete();

            $table->unsignedBigInteger('chain_id');

            $table->string('contract_address', 42);

            $table->string('event_name', 100);

            $table->string('transaction_hash', 66);

            $table->unsignedInteger('log_index');

            $table->unsignedBigInteger('block_number');

            $table->string('block_hash', 66)->nullable();

            $table->json('event_data');

            $table->string('processing_status', 30)
                ->default('pending');

            $table->timestamp('processed_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique(
                ['chain_id', 'transaction_hash', 'log_index'],
                'blockchain_event_unique'
            );

            $table->index(['document_id', 'event_name']);

            $table->index(['document_version_id', 'event_name']);

            $table->index(['processing_status', 'block_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blockchain_events');
    }
};

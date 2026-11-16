
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('wallet_address', 42)->unique();

            $table->string('network_name', 100)->nullable();
            $table->unsignedBigInteger('chain_id')->nullable();

            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_wallets');
    }
};

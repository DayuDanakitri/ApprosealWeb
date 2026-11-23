<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->foreignId('approval_policy_id')
                ->nullable()
                ->after('description')
                ->constrained('approval_policies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropForeign(['approval_policy_id']);
            $table->dropColumn('approval_policy_id');
        });
    }
};

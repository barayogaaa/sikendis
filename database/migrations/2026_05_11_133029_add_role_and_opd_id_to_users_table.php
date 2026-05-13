<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('opd_id')->nullable()->after('id')->constrained('opds')->nullOnDelete();
            $table->string('role')->default('user_opd')->after('email');
            $table->boolean('aktif')->default(true)->after('role');
            $table->index(['role', 'opd_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opd_id');
            $table->dropIndex(['role', 'opd_id']);
            $table->dropColumn(['role', 'aktif']);
        });
    }
};

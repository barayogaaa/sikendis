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
        Schema::create('riwayat_plat_nomors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('plat_nomor_lama', 30);
            $table->string('plat_nomor_baru', 30);
            $table->date('tanggal_perubahan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['kendaraan_id', 'tanggal_perubahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_plat_nomors');
    }
};

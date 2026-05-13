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
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->constrained('opds')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('plat_nomor', 30)->nullable();
            $table->string('merk');
            $table->string('tipe')->nullable();
            $table->year('tahun')->nullable();
            $table->string('nomor_rangka')->nullable()->index();
            $table->string('nomor_mesin')->nullable()->index();
            $table->string('nomor_bpkb')->nullable()->index();
            $table->string('nama_pemilik_di_bpkb')->nullable();
            $table->string('pengguna_penanggung_jawab')->nullable();
            $table->string('scan_bpkb')->nullable();
            $table->string('status_verifikasi')->default('draft')->index();
            $table->text('catatan_admin')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['opd_id', 'status_verifikasi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};

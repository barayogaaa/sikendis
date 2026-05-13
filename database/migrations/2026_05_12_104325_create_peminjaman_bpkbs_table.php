<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_bpkbs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opd_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dipinjamkan_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dikembalikan_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_rencana_pinjam')->nullable();
            $table->date('tanggal_rencana_kembali')->nullable();
            $table->string('keperluan')->nullable();
            $table->string('nama_pengambil')->nullable();
            $table->string('nip_pengambil', 30)->nullable();
            $table->string('status', 30)->default('diajukan');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('dipinjamkan_at')->nullable();
            $table->timestamp('dikembalikan_at')->nullable();
            $table->timestamps();

            $table->index(['opd_id', 'status']);
            $table->index(['kendaraan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_bpkbs');
    }
};

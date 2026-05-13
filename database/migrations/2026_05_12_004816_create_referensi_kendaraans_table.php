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
        Schema::create('referensi_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('import_key')->unique();
            $table->string('plat_nomor', 30)->nullable()->index();
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->year('tahun')->nullable();
            $table->string('nomor_rangka')->nullable()->index();
            $table->string('nomor_mesin')->nullable()->index();
            $table->string('nomor_bpkb')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referensi_kendaraans');
    }
};

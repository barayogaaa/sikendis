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
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->string('scan_stnk')->nullable()->after('scan_bpkb');
            $table->string('foto_kendaraan')->nullable()->after('scan_stnk');
            $table->dropColumn('nama_pemilik_di_bpkb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kendaraans', function (Blueprint $table) {
            $table->string('nama_pemilik_di_bpkb')->nullable()->after('nomor_bpkb');
            $table->dropColumn(['scan_stnk', 'foto_kendaraan']);
        });
    }
};

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
        Schema::create('tempat_timbulan_sampah_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('tempat_timbulan_sampah_sektors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tts_kategori_id')->comment('ID Kategori Tempat Timbulan Sampah');
            $table->foreign('tts_kategori_id')->references('id')->on('tempat_timbulan_sampah_kategoris')->onDelete('restrict');
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('tempat_timbulan_sampahs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_tempat');
            $table->unsignedBigInteger('tts_kategori_id')->comment('ID Kategori Tempat Timbulan Sampah');
            $table->foreign('tts_kategori_id')->references('id')->on('tempat_timbulan_sampah_kategoris')->onDelete('restrict');
            $table->unsignedBigInteger('tts_sektor_id')->nullable()->comment('ID Sektor Tempat Timbulan Sampah');
            $table->foreign('tts_sektor_id')->references('id')->on('tempat_timbulan_sampah_sektors')->onDelete('restrict');
            $table->string('alamat_tempat');
            $table->string('afiliasi')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->decimal('luas_lahan', 10, 2);
            $table->decimal('luas_bangunan', 10, 2);
            $table->decimal('panjang', 10, 2);
            $table->decimal('lebar', 10, 2);
            $table->decimal('sisa_lahan', 10, 2);
            $table->string('kepemilikan_lahan');
            $table->string('foto_tempat')->nullable();
            $table->string('status');
            $table->uuid('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->uuid('updated_by');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tempat_timbulan_sampah_kategoris');
        Schema::dropIfExists('tempat_timbulan_sampah_sektors');
        Schema::dropIfExists('tempat_timbulan_sampahs');
    }
};

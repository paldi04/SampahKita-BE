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
            $table->string('id')->primary();
            $table->string('nama');
            $table->timestamps();
        });
        Schema::create('tempat_timbulan_sampah_sektors', function (Blueprint $table) {
            $table->id();
            $table->string('tts_kategori_id')->comment('ID Kategori Tempat Timbulan Sampah');
            $table->foreign('tts_kategori_id')->references('id')->on('tempat_timbulan_sampah_kategoris')->onDelete('restrict')->onUpdate('cascade');
            $table->string('nama');
            $table->timestamps();
        });
        Schema::create('tempat_timbulan_sampahs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_tempat');
            $table->string('tts_kategori_id')->comment('ID Kategori Tempat Timbulan Sampah');
            $table->foreign('tts_kategori_id')->references('id')->on('tempat_timbulan_sampah_kategoris')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('tts_sektor_id')->nullable()->comment('ID Sektor Tempat Timbulan Sampah');
            $table->foreign('tts_sektor_id')->references('id')->on('tempat_timbulan_sampah_sektors')->onDelete('restrict')->onUpdate('cascade');
            $table->string('afiliasi')->nullable();
            $table->string('alamat_provinsi');
            $table->string('alamat_kota');
            $table->string('alamat_rw');
            $table->string('alamat_rt');
            $table->string('alamat_lengkap');
            $table->string('alamat_latitude');
            $table->string('alamat_longitude');
            $table->decimal('luas_lahan', 10, 2);
            $table->decimal('luas_bangunan', 10, 2);
            $table->decimal('panjang', 10, 2);
            $table->decimal('lebar', 10, 2);
            $table->decimal('sisa_lahan', 10, 2);
            $table->string('kepemilikan_lahan');
            $table->text('foto_tempat')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('tidak_aktif');
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->uuid('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
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

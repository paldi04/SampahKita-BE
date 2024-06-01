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
        Schema::create('sampah_kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('sampah_masuks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tts_id')->nullable()->comment('ID Tempat Timbulan Sampah');
            $table->foreign('tts_id')->references('id')->on('tempat_timbulan_sampahs')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('sampah_kategori_id')->comment('ID Kategori Sampah');
            $table->foreign('sampah_kategori_id')->references('id')->on('sampah_kategoris')->onDelete('restrict')->onUpdate('cascade');
            $table->text('foto_sampah');
            $table->dateTime('waktu_masuk');
            $table->decimal('berat_kg', 10, 2);
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->uuid('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('sampah_diolahs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tss_id')->nullable()->comment('ID Tempat Sumber Sampah');
            $table->foreign('tss_id')->references('id')->on('tempat_timbulan_sampahs')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('sampah_kategori_id')->comment('ID Kategori Sampah');
            $table->foreign('sampah_kategori_id')->references('id')->on('sampah_kategoris')->onDelete('restrict')->onUpdate('cascade');
            $table->decimal('berat_kg', 10, 2);
            $table->string('diolah_oleh');
            $table->uuid('tks_id')->nullable()->comment('ID Tempat Kumpulan Sampah jika diolah oleh TKS');
            $table->foreign('tks_id')->references('id')->on('tempat_timbulan_sampahs')->onDelete('restrict')->onUpdate('cascade');
            $table->dateTime('waktu_diolah');
            $table->enum('status', ['menunggu_respon', 'sudah_direspon', 'dibatalkan'])->default('menunggu_respon');
            $table->text('keterangan')->nullable();
            $table->uuid('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->uuid('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('sampah_dimanfaatkans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tts_id')->nullable()->comment('ID Tempat Timbulan Sampah');
            $table->foreign('tts_id')->references('id')->on('tempat_timbulan_sampahs')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('sampah_kategori_id')->comment('ID Kategori Sampah');
            $table->foreign('sampah_kategori_id')->references('id')->on('sampah_kategoris')->onDelete('restrict')->onUpdate('cascade');
            $table->decimal('berat_kg', 10, 2);
            $table->string('nama_produk');
            $table->decimal('nilai_jual', 10, 2);
            $table->integer('jumlah_produk');
            $table->string('kategori_produk');
            $table->text('foto_produk');
            $table->string('kode_produk');
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
        Schema::dropIfExists('sampah_kategoris');
        Schema::dropIfExists('sampah_masuks');
        Schema::dropIfExists('sampah_diolahs');
        Schema::dropIfExists('sampah_dimanfaatkans');
    }
};

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
        Schema::create('distribusi_sampah_dimanfaatkans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('sampah_dimanfaatkan_id')->nullable()->comment('ID Produk Sampah Dimanfaatkan');
            $table->foreign('sampah_dimanfaatkan_id')->references('id')->on('sampah_dimanfaatkans')->onDelete('restrict')->onUpdate('cascade');
            $table->integer('jumlah_produk');
            $table->uuid('tts_distribusi_id')->nullable()->comment('ID TKS Tempat Distribusi');
            $table->foreign('tts_distribusi_id')->references('id')->on('tempat_timbulan_sampahs')->onDelete('restrict')->onUpdate('cascade');
            $table->text('alamat_distribusi');
            $table->text('link_online_distribusi');
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
        Schema::dropIfExists('distribusi_sampah_dimanfaatkans');
    }
};

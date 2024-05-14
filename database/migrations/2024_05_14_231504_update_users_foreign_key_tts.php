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
            $table->uuid('tts_id')->nullable()->comment('ID Tempat Timbulan Sampah');
            $table->foreign('tts_id')->references('id')->on('tempat_timbulan_sampahs')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tts_id']);
            $table->dropColumn('tts_id');
        });
    }
};

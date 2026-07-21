<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini sudah ada duluan di database (dibuat manual / migration
     * lamanya hilang dari repo). Migration ini hanya "mendaftarkan" bahwa
     * tabel sessions sudah beres, tanpa mencoba create ulang -> aman
     * dijalankan meskipun tabelnya sudah ada.
     */
    public function up(): void
    {
        if (Schema::hasTable('sessions')) {
            return;
        }

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        // Sengaja tidak drop di sini karena tabel ini "titipan" (sudah ada
        // sebelum migration ini dibuat) — hindari kehilangan data sesi tanpa sengaja.
    }
};
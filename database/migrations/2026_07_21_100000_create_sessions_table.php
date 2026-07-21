<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini WAJIB ada karena config/session.php -> SESSION_DRIVER=database.
     * Tanpa tabel ini, StartSession middleware gagal baca/tulis session di
     * SETIAP request web (termasuk GET /login), sehingga CSRF token yang
     * disimpan di session tidak pernah persist antar request -> token
     * mismatch -> 419 Page Expired, meskipun request-nya cuma GET.
     */
    public function up(): void
    {
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
        Schema::dropIfExists('sessions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // contoh: "Juni 2026"
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');
            $table->unsignedBigInteger('monthly_due')->default(0); // iuran per anggota (rupiah)
            $table->unsignedBigInteger('starting_balance')->default(0); // kas awal / manual adjustment
            $table->boolean('is_active')->default(false); // periode aktif / arsip
            $table->timestamps();

            $table->unique(['month', 'year']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_periods');
    }
};

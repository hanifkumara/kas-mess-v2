<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount')->default(0); // nominal dibayar (rupiah)
            $table->date('paid_at')->nullable();             // tanggal bayar
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->string('method')->nullable();            // tunai, transfer, dll
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['cash_period_id', 'member_id']); // satu record iuran per anggota per periode
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name');
            $table->string('category')->nullable(); // Air, Listrik, Beras, Gas, Sabun, IPL, Lainnya
            $table->unsignedBigInteger('amount')->default(0); // harga (rupiah)
            $table->date('expense_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cash_period_id', 'expense_date']);
            $table->index('expense_batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

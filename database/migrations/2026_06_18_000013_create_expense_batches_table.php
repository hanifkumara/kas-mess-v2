<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_period_id')->constrained()->cascadeOnDelete();
            $table->string('title');                  // contoh: "Batch 1"
            $table->date('batch_date')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cash_period_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_batches');
    }
};

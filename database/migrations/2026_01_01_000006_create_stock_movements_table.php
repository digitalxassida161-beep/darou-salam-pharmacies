<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->restrictOnDelete();
            $table->foreignId('medicine_lot_id')->constrained('medicine_lots')->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('type');
            $table->integer('quantity');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void { Schema::dropIfExists('stock_movements'); }
};

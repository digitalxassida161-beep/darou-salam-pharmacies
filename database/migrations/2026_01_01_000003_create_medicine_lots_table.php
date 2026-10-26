<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('medicine_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->restrictOnDelete();
            $table->string('lot_number');
            $table->date('expiry_date');
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->timestamps();
            $table->index(['medicine_id', 'expiry_date']);
        });
    }

    public function down(): void { Schema::dropIfExists('medicine_lots'); }
};

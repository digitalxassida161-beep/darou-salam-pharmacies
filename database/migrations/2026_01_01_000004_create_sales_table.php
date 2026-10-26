<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('patient_label')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_method')->default('espèces');
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void { Schema::dropIfExists('sales'); }
};

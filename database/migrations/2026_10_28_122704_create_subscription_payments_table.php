
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('license_id')
                ->constrained('licenses')
                ->cascadeOnDelete();

            $table->decimal('amount', 12, 2);
            $table->date('period_start');
            $table->date('period_end');
            $table->date('payment_date');

            $table->string('payment_method', 30); // Wave, Orange Money, espèces...
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};

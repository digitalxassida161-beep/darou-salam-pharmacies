
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();

            $table->string('license_key')->unique();
            $table->string('pharmacy_name');
            $table->string('pharmacy_address')->nullable();
            $table->string('pharmacy_phone', 30)->nullable();

            $table->date('installation_date');
            $table->date('maintenance_expires_at')->nullable();

            $table->enum('status', [
                'active',
                'suspended',
                'expired',
            ])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};

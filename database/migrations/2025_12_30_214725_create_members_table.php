<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('dni')->unique();
            $table->string('address');
            $table->string('phone')->nullable();
            
            // Llaves Foráneas (FK)
            $table->foreignId('location_id')->constrained('locations', 'id_location');
            $table->foreignId('account_status_id')->constrained('account_statuses', 'id_account_status');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

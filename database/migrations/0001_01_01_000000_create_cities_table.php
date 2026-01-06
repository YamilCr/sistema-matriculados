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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: "Comodoro Rivadavia"
            // Relación: Crea la columna province_id y la vincula con la tabla provinces
            $table->foreignId('province_id')
                ->constrained()
                ->onDelete('cascade'); // Si se borra la provincia, se borran sus ciudades
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};

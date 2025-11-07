<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Nombre del cultivo: papa, café, arroz, etc.
            $table->string('variety')->nullable(); // Variedad específica
            $table->decimal('planted_area', 8, 2); // Área sembrada en hectáreas
            $table->date('planting_date'); // Fecha de siembra
            $table->date('expected_harvest_date')->nullable(); // Fecha esperada de cosecha
            $table->enum('status', ['siembra', 'crecimiento', 'cosecha', 'descanso'])->default('siembra');
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();

            // Índices
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('crops');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitude', 10, 8); // Latitud geográfica
            $table->decimal('longitude', 11, 8); // Longitud geográfica
            $table->decimal('temperature', 5, 2); // Temperatura en Celsius
            $table->integer('humidity'); // Humedad relativa %
            $table->decimal('wind_speed', 5, 2); // Velocidad del viento
            $table->integer('precipitation'); // Probabilidad lluvia %
            $table->string('weather_condition'); // Estado del clima
            $table->timestamp('recorded_at'); // Fecha y hora del registro
            $table->timestamps(); // created_at, updated_at

            // Índices para mejor rendimiento
            $table->index(['latitude', 'longitude']);
            $table->index('recorded_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('weather_data');
    }
};

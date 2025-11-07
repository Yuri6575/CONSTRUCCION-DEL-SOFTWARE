<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con users
            $table->string('alert_type'); // helada, sequía, granizo
            $table->string('severity'); // baja, media, alta
            $table->text('message'); // Mensaje de la alerta
            $table->decimal('latitude', 10, 8); // Ubicación de la alerta
            $table->decimal('longitude', 11, 8);
            $table->boolean('is_sent')->default(false); // ¿Fue enviada?
            $table->timestamp('alert_time'); // Cuándo se generó
            $table->timestamps();

            // Índices
            $table->index(['user_id', 'is_sent']);
            $table->index('alert_type');
            $table->index('alert_time');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alerts');
    }
};

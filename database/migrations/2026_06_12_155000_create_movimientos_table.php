<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();

            // El usuario que realiza la accion
            $table->foreignId('user_id')->constrained('users');

            // El objeto involucrado
            $table->foreignId('objeto_id')->constrained('objetos');

            // Quien registro el movimiento
            $table->foreignId('registrado_por')->constrained('users');

            $table->string('tipo_movimiento', 20);
            $table->timestamp('fecha_hora')->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};

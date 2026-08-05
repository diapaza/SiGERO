<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cambia la columna `whatsapp_number` a char(9) obligatorio.
     *
     * Las filas existentes con valor NULL se rellenan con un marcador de
     * 9 dígitos ('000000000') para que la migración pueda aplicar NOT NULL.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('whatsapp_number')
            ->update(['whatsapp_number' => '000000000']);

        Schema::table('users', function (Blueprint $table) {
            $table->char('whatsapp_number', 9)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number', 15)->nullable()->change();
        });
    }
};

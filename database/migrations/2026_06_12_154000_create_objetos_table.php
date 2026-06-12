<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objetos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->unique();
            $table->string('nombre', 150);
            $table->string('modelo', 250)->nullable();
            $table->text('descripcion')->nullable();
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('foto', 2048)->nullable();
            $table->string('serie', 50)->nullable();
            $table->boolean('disponible')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objetos');
    }
};

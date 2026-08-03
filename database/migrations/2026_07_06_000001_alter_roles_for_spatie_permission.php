<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('guard_name')->default('web');
            $table->dropUnique(['nombre']);
            $table->renameColumn('nombre', 'name');
            $table->unique(['name', 'guard_name']);
            $table->dropSoftDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['name', 'guard_name']);
            $table->dropColumn('guard_name');
            $table->renameColumn('name', 'nombre');
            $table->unique('nombre');
            $table->softDeletes();
        });
    }
};

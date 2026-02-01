<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('convocatorias', function (Blueprint $table) {
            $table->text('descripcion')->nullable();
            $table->string('enlace')->nullable()->after('descripcion');
            $table->string('etiqueta')->nullable()->after('enlace');
        });
    }

    public function down(): void
    {
        Schema::table('convocatorias', function (Blueprint $table) {
            $table->dropColumn('descripcion');
            $table->dropColumn('enlace');
            $table->dropColumn('etiqueta');
        });
    }
};


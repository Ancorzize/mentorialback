<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('opciones', function (Blueprint $table) {
            $table->text('retroalimentacion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('opciones', function (Blueprint $table) {
            $table->dropColumn('retroalimentacion');
        });
    }
};
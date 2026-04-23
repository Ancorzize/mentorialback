<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // paginaabierta, sesionesiniciadas, etc.
            $table->date('fecha');  
            $table->unsignedBigInteger('total')->default(0);
            $table->timestamps();

            $table->unique(['tipo', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
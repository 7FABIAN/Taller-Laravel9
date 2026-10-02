<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_simple', function (Blueprint $table) {
          $table->id();
    $table->string('nombre_empresa');
    $table->string('contacto_principal');
    $table->string('telefono_whatsapp');
    $table->string('zona_geografica');
    $table->foreignId('user_id');
    $table->foreignId('origin_id');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_simple');
    }
};

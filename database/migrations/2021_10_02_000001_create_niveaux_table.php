<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('niveaux', function (Blueprint $table) {
            $table->tinyIncrements('id_niveau');
            $table->string('libelle_niveau');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('niveaux');
    }
};

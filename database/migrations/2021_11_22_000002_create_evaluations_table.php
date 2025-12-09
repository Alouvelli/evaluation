<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_professeur');
            $table->unsignedBigInteger('id_cours');
            $table->unsignedBigInteger('idQ');
            $table->unsignedTinyInteger('note');
            $table->timestamps();

            $table->foreign('id_professeur')->references('id')->on('professeurs')->onDelete('cascade');
            $table->foreign('id_cours')->references('id_cours')->on('cours')->onDelete('cascade');
            $table->foreign('idQ')->references('idQ')->on('questions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};

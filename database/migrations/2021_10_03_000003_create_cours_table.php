<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cours', function (Blueprint $table) {
            $table->id('id_cours');
            $table->tinyInteger('etat')->default(0);
            $table->string('libelle_cours');
            $table->unsignedBigInteger('id_classe');
            $table->unsignedBigInteger('id_professeur');
            $table->smallInteger('semestre');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('annee_id');
            $table->timestamps();

            $table->foreign('id_classe')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('id_professeur')->references('id')->on('professeurs')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campus')->onDelete('cascade');
            $table->foreign('annee_id')->references('id')->on('annee_academique')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};

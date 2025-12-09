<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtudiantsTable extends Migration
{
    public function up()
    {
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 50)->unique();
            $table->unsignedInteger('statut')->default(0);
            $table->unsignedBigInteger('id_classe');
            $table->unsignedBigInteger('campus_id');
            $table->timestamps();

            $table->foreign('id_classe')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campus')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('etudiants');
    }
}

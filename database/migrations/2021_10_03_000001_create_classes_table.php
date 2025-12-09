<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('libelle');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedTinyInteger('id_niveau');
            $table->timestamps();

            $table->foreign('campus_id')->references('id')->on('campus')->onDelete('cascade');
            $table->foreign('id_niveau')->references('id_niveau')->on('niveaux')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};

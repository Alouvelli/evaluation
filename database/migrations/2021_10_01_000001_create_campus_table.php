<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus', function (Blueprint $table) {
            $table->id();
            $table->string('nomCampus', 50);
        });

        // Ajouter la contrainte FK sur users
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('campus_id')->references('id')->on('campus')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['campus_id']);
        });
        Schema::dropIfExists('campus');
    }
};

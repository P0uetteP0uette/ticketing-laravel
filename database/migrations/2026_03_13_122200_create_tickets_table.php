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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->enum('type', ['inclus','facturable'])->default('inclus');
            $table->enum('statut', ['Nouveau','En cours','Terminé'])->default('Nouveau');
            $table->enum('priorite', ['Basse','Moyenne','Haute','Critique'])->default('Moyenne');
            
            $table->foreignId('projet_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('auteur_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

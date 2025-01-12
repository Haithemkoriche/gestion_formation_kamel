<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id('id_employe');
            $table->string('nom');
            $table->string('prenom');
            $table->string('grade');
            $table->string('matricule')->unique();
            $table->string('diplome');
            $table->string('unite');
            $table->date('date_de_recrutement');
            $table->date('date_de_diplome');
            $table->string('type_de_diplome');
            $table->string('specialisation');
            $table->date('date_de_prise_service');
            $table->date('date_de_naissance');
            $table->date('date_nomination_actuelle');
            $table->string('penalites')->nullable();
            $table->string('connaissances')->nullable();
            $table->string('decision_de_fonction');
            $table->integer('cycle_annee');
            $table->string('duree_fonction');
            $table->integer('debut');
            $table->integer('fin');
            $table->string('situation');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employes');
    }
};

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
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id('id_etudiant'); // Clé primaire
            $table->string('nom');
            $table->string('prenom');
            $table->string('sexe');
            $table->date('date_de_naissance');
            $table->string('lieu_de_naissance');
            $table->string('pays');
            $table->string('nationalite');
            $table->string('adresse_actuelle');
            $table->string('situation_familiale');
            $table->string('numero_telephone');
            $table->string('niveau_scolaire');
            $table->string('grade');
            $table->string('matricule')->unique();
            $table->string('unite');
            $table->string('nom_entreprise');
            $table->date('date_de_recrutement');
            $table->string('mode_engagement');
            $table->date('date_mise_a_niveau');
            $table->date('date_nomination_active');
            $table->string('fonction_actuelle');
            $table->string('annee_scolaire');
            $table->string('type_formation');
            $table->string('nature_diplome');
            $table->string('specialite');
            $table->string('specialite_partielle');
            $table->string('lieu_formation');
            $table->string('annee_formation');
            $table->string('duree_formation');
            $table->string('diplome_obtenu');
            $table->string('formations_precedentes');
            $table->date('date_obtention_diplome');
            $table->string('diplomes_precedents');
            $table->string('autres_diplomes');
            $table->string('cycle');
            $table->date('date_debut_formation');
            $table->date('date_fin_formation');
            $table->string('punition')->nullable();
            $table->string('convalescences')->nullable();
            $table->string('etat_sante')->nullable();
            $table->timestamps(); // Ajoute les colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etudiants');
    }
};

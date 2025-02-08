<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    use HasFactory;

    // Nom de la table (optionnel si vous suivez les conventions de Laravel)
    protected $table = 'etudiants';

    // Clé primaire (optionnel si vous utilisez 'id' comme clé primaire)
    protected $primaryKey = 'id_etudiant';

    // Champs assignables en masse
    protected $fillable = [
        'nom',
        'prenom',
        'sexe',
        'date_de_naissance',
        'lieu_de_naissance',
        'pays',
        'nationalite',
        'adresse_actuelle',
        'situation_familiale',
        'numero_telephone',
        'niveau_scolaire',
        'grade',
        'matricule',
        'unite',
        'nom_entreprise',
        'date_de_recrutement',
        'mode_engagement',
        'date_mise_a_niveau',
        'date_nomination_active',
        'fonction_actuelle',
        'annee_scolaire',
        'type_formation',
        'nature_diplome',
        'specialite',
        'specialite_partielle',
        'lieu_formation',
        'annee_formation',
        'duree_formation',
        'diplome_obtenu',
        'formations_precedentes',
        'date_obtention_diplome',
        'diplomes_precedents',
        'autres_diplomes',
        'cycle',
        'date_debut_formation',
        'date_fin_formation',
        'punition',
        'convalescences',
        'etat_sante',
    ];

    // Dates automatiquement gérées par Eloquent
    protected $dates = [
        'date_de_naissance',
        'date_de_recrutement',
        'date_mise_a_niveau',
        'date_nomination_active',
        'date_obtention_diplome',
        'date_debut_formation',
        'date_fin_formation',
        'created_at',
        'updated_at',
    ];
}

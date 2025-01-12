<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    protected $primaryKey = 'id_employe';
    
    protected $fillable = [
        'nom',
        'prenom',
        'grade',
        'matricule',
        'diplome',
        'unite',
        'date_de_recrutement',
        'date_de_diplome',
        'type_de_diplome',
        'specialisation',
        'date_de_prise_service',
        'date_de_naissance',
        'date_nomination_actuelle',
        'penalites',
        'connaissances',
        'decision_de_fonction',
        'cycle_annee',
        'duree_fonction',
        'debut',
        'fin',
        'situation'
    ];

    protected $casts = [
        'date_de_recrutement' => 'date',
        'date_de_diplome' => 'date',
        'date_de_prise_service' => 'date',
        'date_de_naissance' => 'date',
        'date_nomination_actuelle' => 'date',
        'cycle_annee' => 'integer',
        'debut' => 'integer',
        'fin' => 'integer'
    ];
}

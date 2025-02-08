<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    protected $primaryKey = 'id_employe';

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
        'status', 

    ];

    protected $casts = [
        'date_de_naissance' => 'date',
        'date_de_recrutement' => 'date',
        'date_mise_a_niveau' => 'date',
        'date_nomination_active' => 'date',
        'date_obtention_diplome' => 'date',
        'date_debut_formation' => 'date',
        'date_fin_formation' => 'date',
    ];

    // Accessors pour convertir les chaînes JSON en tableaux
    public function getTypeFormationAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getSpecialiteAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getSpecialitePartielleAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getFormationsPrecedentesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getDiplomesPrecedentsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getAutresDiplomesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getPunitionAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getConvalescencesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getAnneeFormationAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function getDiplomeObtenuAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    // Mutators pour convertir les tableaux en chaînes JSON avant l'enregistrement
    public function setTypeFormationAttribute($value)
    {
        $this->attributes['type_formation'] = json_encode($value);
    }

    public function setSpecialiteAttribute($value)
    {
        $this->attributes['specialite'] = json_encode($value);
    }

    public function setSpecialitePartielleAttribute($value)
    {
        $this->attributes['specialite_partielle'] = json_encode($value);
    }

    public function setFormationsPrecedentesAttribute($value)
    {
        $this->attributes['formations_precedentes'] = json_encode($value);
    }

    public function setDiplomesPrecedentsAttribute($value)
    {
        $this->attributes['diplomes_precedents'] = json_encode($value);
    }

    public function setAutresDiplomesAttribute($value)
    {
        $this->attributes['autres_diplomes'] = json_encode($value);
    }

    public function setPunitionAttribute($value)
    {
        $this->attributes['punition'] = json_encode($value);
    }

    public function setConvalescencesAttribute($value)
    {
        $this->attributes['convalescences'] = json_encode($value);
    }

    public function setAnneeFormationAttribute($value)
    {
        $this->attributes['annee_formation'] = json_encode($value);
    }

    public function setDiplomeObtenuAttribute($value)
    {
        $this->attributes['diplome_obtenu'] = json_encode($value);
    }
}

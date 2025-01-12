<?php

namespace App\Exports;

use App\Models\Employe;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployesExport implements FromCollection, WithHeadings, WithMapping
{
    private $employes;

    public function __construct($employes = null)
    {
        $this->employes = $employes;
    }

    public function collection()
    {
        return $this->employes ?? Employe::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Prénom',
            'Grade',
            'Matricule',
            'Diplôme',
            'Unité',
            'Date de Recrutement',
            'Date de Diplôme',
            'Type de Diplôme',
            'Spécialisation',
            'Date de Prise Service',
            'Date de Naissance',
            'Date Nomination Actuelle',
            'Pénalités',
            'Connaissances',
            'Décision de Fonction',
            'Cycle Année',
            'Durée Fonction',
            'Début',
            'Fin',
            'Situation'
        ];
    }

    public function map($employe): array
    {
        return [
            $employe->id_employe,
            $employe->nom,
            $employe->prenom,
            $employe->grade,
            $employe->matricule,
            $employe->diplome,
            $employe->unite,
            $employe->date_de_recrutement,
            $employe->date_de_diplome,
            $employe->type_de_diplome,
            $employe->specialisation,
            $employe->date_de_prise_service,
            $employe->date_de_naissance,
            $employe->date_nomination_actuelle,
            $employe->penalites,
            $employe->connaissances,
            $employe->decision_de_fonction,
            $employe->cycle_annee,
            $employe->duree_fonction,
            $employe->debut,
            $employe->fin,
            $employe->situation
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\EtudiantsExport;
use App\Models\Employe;
use App\Models\Etudiant;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class EtudiantController extends Controller
{
    public function index(Request $request)
    {
        $query = Etudiant::query();

        // Fonctionnalité de recherche
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'LIKE', "%{$request->search}%")
                    ->orWhere('prenom', 'LIKE', "%{$request->search}%")
                    ->orWhere('matricule', 'LIKE', "%{$request->search}%")
                    ->orWhere('unite', 'LIKE', "%{$request->search}%");
            });
        }

        // Filtres
        if ($request->grade) {
            $query->where('grade', $request->grade);
        }
        if ($request->unite) {
            $query->where('unite', $request->unite);
        }

        return $query->orderBy('nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'string|max:255',
            'prenom' => 'string|max:255',
            'sexe' => 'string|max:255',
            'date_de_naissance' => 'date',
            'lieu_de_naissance' => 'string|max:255',
            'pays' => 'string|max:255',
            'nationalite' => 'string|max:255',
            'adresse_actuelle' => 'string|max:255',
            'situation_familiale' => 'string|max:255',
            'numero_telephone' => 'string|max:255',
            'niveau_scolaire' => 'string|max:255',
            'grade' => 'string|max:255',
            'matricule' => 'string|max:255|unique:etudiants',
            'unite' => 'string|max:255',
            'nom_entreprise' => 'string|max:255',
            'date_de_recrutement' => 'date',
            'mode_engagement' => 'string|max:255',
            'date_mise_a_niveau' => 'date',
            'date_nomination_active' => 'date',
            'fonction_actuelle' => 'string|max:255',
            'annee_scolaire' => 'string|max:255',
            'type_formation' => 'string|max:255',
            'nature_diplome' => 'string|max:255',
            'specialite' => 'string|max:255',
            'specialite_partielle' => 'string|max:255',
            'lieu_formation' => 'string|max:255',
            'annee_formation' => 'string|max:255',
            'duree_formation' => 'string|max:255',
            'diplome_obtenu' => 'string|max:255',
            'formations_precedentes' => 'string|max:255',
            'date_obtention_diplome' => 'date',
            'diplomes_precedents' => 'string|max:255',
            'autres_diplomes' => 'string|max:255',
            'cycle' => 'string|max:255',
            'date_debut_formation' => 'date',
            'date_fin_formation' => 'date',
            'punition' => 'nullable|string|max:255',
            'convalescences' => 'nullable|string|max:255',
            'etat_sante' => 'nullable|string|max:255',
        ]);

        return Etudiant::create($validated);
    }

    public function show(Etudiant $etudiant)
    {
        return $etudiant;
    }

    public function update(Request $request, Etudiant $etudiant)
    {
        $validated = $request->validate([
            'nom' => 'string|max:255',
            'prenom' => 'string|max:255',
            'sexe' => 'string|max:255',
            'date_de_naissance' => 'date',
            'lieu_de_naissance' => 'string|max:255',
            'pays' => 'string|max:255',
            'nationalite' => 'string|max:255',
            'adresse_actuelle' => 'string|max:255',
            'situation_familiale' => 'string|max:255',
            'numero_telephone' => 'string|max:255',
            'niveau_scolaire' => 'string|max:255',
            'grade' => 'string|max:255',
            'matricule' => ['string', 'max:255', Rule::unique('etudiants')->ignore($etudiant->id_etudiant, 'id_etudiant')],
            'unite' => 'string|max:255',
            'nom_entreprise' => 'string|max:255',
            'date_de_recrutement' => 'date',
            'mode_engagement' => 'string|max:255',
            'date_mise_a_niveau' => 'date',
            'date_nomination_active' => 'date',
            'fonction_actuelle' => 'string|max:255',
            'annee_scolaire' => 'string|max:255',
            'type_formation' => 'string|max:255',
            'nature_diplome' => 'string|max:255',
            'specialite' => 'string|max:255',
            'specialite_partielle' => 'string|max:255',
            'lieu_formation' => 'string|max:255',
            'annee_formation' => 'string|max:255',
            'duree_formation' => 'string|max:255',
            'diplome_obtenu' => 'string|max:255',
            'formations_precedentes' => 'string|max:255',
            'date_obtention_diplome' => 'date',
            'diplomes_precedents' => 'string|max:255',
            'autres_diplomes' => 'string|max:255',
            'cycle' => 'string|max:255',
            'date_debut_formation' => 'date',
            'date_fin_formation' => 'date',
            'punition' => 'nullable|string|max:255',
            'convalescences' => 'nullable|string|max:255',
            'etat_sante' => 'nullable|string|max:255',
        ]);

        $etudiant->update($validated);
        return $etudiant;
    }

    public function destroy(Etudiant $etudiant)
    {
        $etudiant->delete();
        return response()->json(null, 204);
    }

    public function exportExcel(Request $request)
    {
        $etudiants = $this->getFilteredEtudiants($request);
        return Excel::download(new EtudiantsExport($etudiants), 'etudiants.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $etudiants = $this->getFilteredEtudiants($request);
        $pdf = FacadePdf::loadView('exports.etudiants', compact('etudiants'));
        return $pdf->download('etudiants.pdf');
    }

    public function exportSinglePDF(Etudiant $etudiant)
    {
        $pdf = FacadePdf::loadView('exports.etudiant-single', compact('etudiant'));
        return $pdf->download("etudiant-{$etudiant->id_etudiant}.pdf");
    }

    private function getFilteredEtudiants(Request $request)
    {
        $query = Etudiant::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'LIKE', "%{$request->search}%")
                    ->orWhere('prenom', 'LIKE', "%{$request->search}%")
                    ->orWhere('matricule', 'LIKE', "%{$request->search}%");
            });
        }

        return $query->get();
    }
    // Dans EtudiantController.php
    public function convertToEmployee($id)
    {
        try {
            // Récupérer l'étudiant
            $etudiant = Etudiant::findOrFail($id);

            // Vérifier si l'étudiant existe déjà en tant qu'employé
            $employeExiste = Employe::where('matricule', $etudiant->matricule)->exists();
            if ($employeExiste) {
                return response()->json(['message' => 'Cet étudiant est déjà converti en employé.'], 400);
            }

            // Créer un nouvel employé avec les données de l'étudiant
            $employe = Employe::create([
                'nom' => $etudiant->nom,
                'prenom' => $etudiant->prenom,
                'sexe' => $etudiant->sexe,
                'date_de_naissance' => $etudiant->date_de_naissance,
                'lieu_de_naissance' => $etudiant->lieu_de_naissance,
                'pays' => $etudiant->pays,
                'nationalite' => $etudiant->nationalite,
                'adresse_actuelle' => $etudiant->adresse_actuelle,
                'situation_familiale' => $etudiant->situation_familiale,
                'numero_telephone' => $etudiant->numero_telephone,
                'niveau_scolaire' => $etudiant->niveau_scolaire,
                'grade' => $etudiant->grade,
                'matricule' => $etudiant->matricule,
                'unite' => $etudiant->unite,
                'nom_entreprise' => $etudiant->nom_entreprise,
                'date_de_recrutement' => $etudiant->date_de_recrutement,
                'mode_engagement' => $etudiant->mode_engagement,
                'date_mise_a_niveau' => $etudiant->date_mise_a_niveau,
                'date_nomination_active' => $etudiant->date_nomination_active,
                'fonction_actuelle' => $etudiant->fonction_actuelle,
                'annee_scolaire' => $etudiant->annee_scolaire,
                'type_formation' => $etudiant->type_formation,
                'nature_diplome' => $etudiant->nature_diplome,
                'specialite' => $etudiant->specialite,
                'specialite_partielle' => $etudiant->specialite_partielle,
                'lieu_formation' => $etudiant->lieu_formation,
                'annee_formation' => $etudiant->annee_formation,
                'duree_formation' => $etudiant->duree_formation,
                'diplome_obtenu' => $etudiant->diplome_obtenu,
                'formations_precedentes' => $etudiant->formations_precedentes,
                'date_obtention_diplome' => $etudiant->date_obtention_diplome,
                'diplomes_precedents' => $etudiant->diplomes_precedents,
                'autres_diplomes' => $etudiant->autres_diplomes,
                'cycle' => $etudiant->cycle,
                'date_debut_formation' => $etudiant->date_debut_formation,
                'date_fin_formation' => $etudiant->date_fin_formation,
                'punition' => $etudiant->punition,
                'convalescences' => $etudiant->convalescences,
                'etat_sante' => $etudiant->etat_sante,
            ]);

            // Supprimer l'étudiant (ou le marquer comme converti)
            // $etudiant->delete(); 
            // Ou $etudiant->update(['converted' => true]);

            return response()->json([
                'message' => 'Étudiant converti en employé avec succès',
                'employe' => $employe,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la conversion.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

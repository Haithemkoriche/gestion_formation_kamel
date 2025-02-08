<?php

namespace App\Http\Controllers;

use App\Exports\EmployesExport;
use App\Models\Employe;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class EmployeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employe::query();
    
        // Filtrer par statut
        if ($request->status) {
            $query->where('status', $request->status);
        }
    
        // Recherche
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'LIKE', "%{$request->search}%")
                  ->orWhere('prenom', 'LIKE', "%{$request->search}%")
                  ->orWhere('matricule', 'LIKE', "%{$request->search}%");
            });
        }
    
        return $query->orderBy('nom')->get();
    }
    public function stagiaires(Request $request)
    {
        // Filtrer les employés avec le statut "stage"
        $stagiaires = Employe::where('status', 'stage')->get();
        return $stagiaires;

        // return view('stagiaires.index', compact('stagiaires'));
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
            'matricule' => 'string|max:255|unique:employes',
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

        return Employe::create($validated);
    }

    public function show(Employe $employe)
    {
        return $employe;
    }

    public function update(Request $request, Employe $employe)
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
            'matricule' => ['string', 'max:255', Rule::unique('employes')->ignore($employe->id_employe, 'id_employe')],
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

        $employe->update($validated);
        return $employe;
    }

    public function destroy(Employe $employe)
    {
        $employe->delete();
        return response()->json(null, 204);
    }

    public function exportExcel(Request $request)
    {
        $employes = $this->getFilteredEmployes($request);
        return Excel::download(new EmployesExport($employes), 'employes.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $employes = $this->getFilteredEmployes($request);
        $pdf = FacadePdf::loadView('exports.employes', compact('employes'));
        return $pdf->download('employes.pdf');
    }

    public function exportSinglePDF(Employe $employe)
    {
        $pdf = FacadePdf::loadView('exports.employe-single', compact('employe'));
        return $pdf->download("employe-{$employe->id_employe}.pdf");
    }

    private function getFilteredEmployes(Request $request)
    {
        $query = Employe::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'LIKE', "%{$request->search}%")
                    ->orWhere('prenom', 'LIKE', "%{$request->search}%")
                    ->orWhere('matricule', 'LIKE', "%{$request->search}%");
            });
        }

        return $query->get();
    }
    public function convertToStage(Request $request, $id)
    {
        try {
            // Récupérer l'employé
            $employe = Employe::findOrFail($id);
    
            // Valider les données de la requête
            $validated = $request->validate([
                'school_id' => 'required|exists:schools,id',
                'formation_id' => 'required|exists:formations,id',
                'duration' => 'required|integer|min:1',
            ]);
    
            // Mettre à jour le statut de l'employé
            $employe->update([
                'status' => 'stage',
                'school_id' => $validated['school_id'],
                'formation_id' => $validated['formation_id'],
                'stage_duration' => $validated['duration'],
            ]);
    
            return response()->json([
                'message' => 'Employé converti en stage avec succès',
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

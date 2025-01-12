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

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nom', 'LIKE', "%{$request->search}%")
                  ->orWhere('prenom', 'LIKE', "%{$request->search}%")
                  ->orWhere('matricule', 'LIKE', "%{$request->search}%")
                  ->orWhere('unite', 'LIKE', "%{$request->search}%");
            });
        }

        // Filters
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'matricule' => 'required|string|max:255|unique:employes',
            'diplome' => 'required|string|max:255',
            'unite' => 'required|string|max:255',
            'date_de_recrutement' => 'required|date',
            'date_de_diplome' => 'required|date',
            'type_de_diplome' => 'required|string|max:255',
            'specialisation' => 'required|string|max:255',
            'date_de_prise_service' => 'required|date',
            'date_de_naissance' => 'required|date',
            'date_nomination_actuelle' => 'required|date',
            'penalites' => 'nullable|string|max:255',
            'connaissances' => 'nullable|string|max:255',
            'decision_de_fonction' => 'required|string|max:255',
            'cycle_annee' => 'required|integer',
            'duree_fonction' => 'required|string|max:255',
            'debut' => 'required|integer',
            'fin' => 'required|integer',
            'situation' => 'required|string|max:255',
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
            'matricule' => ['required', 'string', 'max:255', Rule::unique('employes')->ignore($employe->id_employe, 'id_employe')],
            'diplome' => 'required|string|max:255',
            'unite' => 'required|string|max:255',
            'date_de_recrutement' => 'required|date',
            'date_de_diplome' => 'required|date',
            'type_de_diplome' => 'required|string|max:255',
            'specialisation' => 'required|string|max:255',
            'date_de_prise_service' => 'required|date',
            'date_de_naissance' => 'required|date',
            'date_nomination_actuelle' => 'required|date',
            'penalites' => 'nullable|string|max:255',
            'connaissances' => 'nullable|string|max:255',
            'decision_de_fonction' => 'required|string|max:255',
            'cycle_annee' => 'required|integer',
            'duree_fonction' => 'required|string|max:255',
            'debut' => 'required|integer',
            'fin' => 'required|integer',
            'situation' => 'required|string|max:255',
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
            $query->where(function($q) use ($request) {
                $q->where('nom', 'LIKE', "%{$request->search}%")
                  ->orWhere('prenom', 'LIKE', "%{$request->search}%")
                  ->orWhere('matricule', 'LIKE', "%{$request->search}%");
            });
        }

        return $query->get();
    }
}

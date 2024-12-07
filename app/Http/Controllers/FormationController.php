<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function index()
    {
        return Formation::with('school', 'category')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'duration' => 'required|integer',
            'school_id' => 'required|exists:schools,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        return Formation::create($request->all());
    }

    public function update(Request $request, Formation $formation)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'duration' => 'required|integer',
            'school_id' => 'required|exists:schools,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $formation->update($request->all());

        return $formation;
    }

    public function destroy(Formation $formation)
    {
        $formation->delete();
        return response()->noContent();
    }
    //
}

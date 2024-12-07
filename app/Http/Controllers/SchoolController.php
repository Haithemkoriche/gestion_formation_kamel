<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schools = School::all();
        return response()->json(['data' => $schools]);
    }

    // Create a new school
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:schools,email',
        ]);

        $school = School::create($validated);
        return response()->json(['message' => 'School created successfully', 'data' => $school], 201);
    }

    // Show a specific school
    public function show(School $school)
    {
        return response()->json(['data' => $school]);
    }

    // Update a school
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:schools,email,' . $school->id,
        ]);

        $school->update($validated);
        return response()->json(['message' => 'School updated successfully', 'data' => $school]);
    }

    // Delete a school
    public function destroy(School $school)
    {
        $school->delete();
        return response()->json(['message' => 'School deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $patient = Patient::query()->with('hospital');

        if ($request->filled('search')) {
            $s = $request->search;
            $patient
                ->where('name', 'LIKE', "%$s%")
                ->orWhere('address', 'LIKE', "%$s%")
                ->orWhere('phone', 'LIKE', "%$s%")
                ->orWhereRelation('hospital', 'name', 'LIKE', "%$s%");
        }

        return $patient->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'address' => 'required',
            'phone' => 'required',
            'hospital_id' => 'required|exists:hospitals,id'
        ]);

        Patient::create($validate);

        return response()->json(['message' => 'Patient created successfully']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'address' => 'required',
            'phone' => 'required',
            'hospital_id' => 'required|exists:hospitals,id'
        ]);

        $patient->update($validate);

        return response()->json(['message' => 'Patient updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return response()->json(['message' => 'Patient deleted successfully']);
    }
}

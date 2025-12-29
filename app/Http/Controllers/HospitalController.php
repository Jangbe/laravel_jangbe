<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $hospital = Hospital::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $hospital
                ->where('name', 'LIKE', "%$s%")
                ->orWhere('address', 'LIKE', "%$s%")
                ->orWhere('email', 'LIKE', "%$s%")
                ->orWhere('phone', 'LIKE', "%$s%");
        }

        return $hospital->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'address' => 'required',
            'email' => 'required|email|unique:hospitals,email',
            'phone' => 'required'
        ]);

        Hospital::create($validate);

        return response()->json(['message' => 'Hospital created successfully']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hospital $hospital)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'address' => 'required',
            'email' => 'required|email|unique:hospitals,email',
            'phone' => 'required'
        ]);

        $hospital->update($validate);

        return response()->json(['message' => 'Hospital updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hospital $hospital)
    {
        $hospital->delete();

        return response()->json(['message' => 'Hospital deleted successfully']);
    }
}

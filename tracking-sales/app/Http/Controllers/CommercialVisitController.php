<?php

namespace App\Http\Controllers;
use App\Models\CommercialVisit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class CommercialVisitController extends Controller
{


// Show all visits created by this user
public function index()
{
    // eager‐load user if you need it
    $visits = CommercialVisit::where('user_id', auth()->id())
                              ->orderBy('visit_date', 'desc')
                              ->get();

    return view('visits.index', compact('visits'));
}

// Show form to create a new visit
public function create()
{
    // If you have cleaning types stored somewhere, fetch them
    $cleaningTypes = [
        "Dry Cleaning",
        "Wash & Fold",
        "Iron Only",
        "Express Service"
    ];

    return view('visits.create', compact('cleaningTypes'));
}

// Handle form POST and save
public function store(Request $request)
{
    $data = $request->validate([
        'client_name'   => 'required|string|max:255',
        'location'      => 'required|string',
        'cleaning_type' => 'required|string',
        'visit_date'    => 'required|date',
        'contact'       => 'required|string',
        'relance_date'  => 'required|date|after_or_equal:visit_date',
    ]);

    // attach current user
    $data['user_id'] = auth()->id();

    CommercialVisit::create($data);

    return redirect()
        ->route('visits.index')
        ->with('success', 'Visite enregistrée avec succès.');
}
}

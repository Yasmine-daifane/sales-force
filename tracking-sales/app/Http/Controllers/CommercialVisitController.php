<?php

namespace App\Http\Controllers;

use App\Models\CommercialVisit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Ensure Auth facade is imported

class CommercialVisitController extends Controller
{
    /**
     * Create a new controller instance.
     * Apply auth middleware to all methods.
     */
    public function __construct()
    {
        $this->middleware("auth");
    }

    /**
     * Display a listing of the resource.
     *
     * Filters visits based on user role (admin sees all, commercial sees own).
     * Includes search functionality and pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input("search");

        // Determine if the user is an admin (adjust the condition based on your role storage)
        $isAdmin = $user && isset($user->role) && strtolower($user->role) === "admin";

        // Start query builder, eager load user for admin view
        $query = CommercialVisit::with("user")->orderBy("visit_date", "desc");

        if (!$isAdmin) {
            // Commercial: Filter by their own user ID
            $query->where("user_id", $user->id);
            $title = "Mes Visites Commerciales";
        } else {
            // Admin: No user filter needed, different title
            $title = "Liste de Toutes les Visites Commerciales";
        }

        // Apply search filter if a search term is provided
        if ($search) {
            $query->where(function ($q) use ($search, $isAdmin) {
                $q->where("client_name", "like", "%{$search}%")
                  ->orWhere("location", "like", "%{$search}%")
                  ->orWhere("cleaning_type", "like", "%{$search}%");

                // If admin, allow searching by commercial name as well
                if ($isAdmin) {
                    $q->orWhereHas("user", function ($userQuery) use ($search) {
                        $userQuery->where("name", "like", "%{$search}%");
                    });
                }
            });
        }

        // Paginate the results
        $visits = $query->paginate(10)->withQueryString(); // Use withQueryString to keep search param in pagination links

        // Pass data to the view
        return view("visits.index", compact("visits", "title", "search"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        // Example cleaning types - consider fetching from DB or config
        $cleaningTypes = [
            "Nettoyage à sec",
            "Lavage et pliage",
            "Repassage seul",
            "Service express"
            // Add other types as needed
        ];

        return view("visits.create", compact("cleaningTypes"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            "client_name"   => "required|string|max:255",
            "location"      => "required|string",
            "cleaning_type" => "required|string",
            "visit_date"    => "required|date",
            "contact"       => "required|string",
            "relance_date"  => "required|date|after_or_equal:visit_date",
        ]);

        // Attach the current authenticated user's ID
        $data["user_id"] = Auth::id();

        CommercialVisit::create($data);

        return redirect()
            ->route("visits.index")
            ->with("success", "Visite enregistrée avec succès.");
    }

    // --- Add other resource methods (show, edit, update, destroy) as needed ---

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CommercialVisit  $visit
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(CommercialVisit $visit)
    {
        // Authorization: Ensure only admin or the owner can edit
        $user = Auth::user();
        $isAdmin = $user && isset($user->role) && strtolower($user->role) === "admin";
        if (!$isAdmin && $visit->user_id !== $user->id) {
            abort(403, "Accès non autorisé.");
        }

        $cleaningTypes = [
            "Nettoyage à sec",
            "Lavage et pliage",
            "Repassage seul",
            "Service express"
        ];
        return view("visits.edit", compact("visit", "cleaningTypes"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CommercialVisit  $visit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CommercialVisit $visit)
    {
        // Authorization: Ensure only admin or the owner can update
        $user = Auth::user();
        $isAdmin = $user && isset($user->role) && strtolower($user->role) === "admin";
        if (!$isAdmin && $visit->user_id !== $user->id) {
            abort(403, "Accès non autorisé.");
        }

        $data = $request->validate([
            "client_name"   => "required|string|max:255",
            "location"      => "required|string",
            "cleaning_type" => "required|string",
            "visit_date"    => "required|date",
            "contact"       => "required|string",
            "relance_date"  => "required|date|after_or_equal:visit_date",
        ]);

        $visit->update($data);

        return redirect()
            ->route("visits.index")
            ->with("success", "Visite mise à jour avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CommercialVisit  $visit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CommercialVisit $visit)
    {
        // Authorization: Ensure only admin or the owner can delete
        $user = Auth::user();
        $isAdmin = $user && isset($user->role) && strtolower($user->role) === "admin";
        if (!$isAdmin && $visit->user_id !== $user->id) {
            abort(403, "Accès non autorisé.");
        }

        $visit->delete();

        return redirect()
            ->route("visits.index")
            ->with("success", "Visite supprimée avec succès.");
    }
}


<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CommercialVisit;
use Illuminate\Http\Request;

class CommercialVisitController extends Controller
{
    // /**
    //  * Afficher la liste des visites du commercial connecté
    //  */
    // public function index(Request $request)
    // {
    //     $visits = CommercialVisit::where('user_id', $request->user()->id)
    //                             ->orderBy('visit_date', 'desc')
    //                             ->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $visits,
    //     ]);
    // }

    // /**
    //  * Enregistrer une nouvelle visite
    //  */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'client_name' => 'required|string|max:255',
    //         'location' => 'required|string|max:255',
    //         'cleaning_type' => 'required|string|max:255',
    //         'visit_date' => 'required|date',
    //         'contact' => 'required|string|max:255',
    //         'relance_date' => 'required|date',
    //     ]);

    //     // Ajouter l'ID de l'utilisateur connecté
    //     $validated['user_id'] = $request->user()->id;

    //     $visit = CommercialVisit::create($validated);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Visite enregistrée avec succès',
    //         'data' => $visit,
    //     ], 201);
    // }

    // /**
    //  * Afficher les détails d'une visite spécifique
    //  */
    // public function show(Request $request, $id)
    // {
    //     $visit = CommercialVisit::where('id', $id)
    //                            ->where('user_id', $request->user()->id)
    //                            ->first();

    //     if (!$visit) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Visite non trouvée',
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $visit,
    //     ]);
    // }

    // /**
    //  * Mettre à jour une visite existante
    //  */
    // public function update(Request $request, $id)
    // {
    //     $visit = CommercialVisit::where('id', $id)
    //                            ->where('user_id', $request->user()->id)
    //                            ->first();

    //     if (!$visit) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Visite non trouvée',
    //         ], 404);
    //     }

    //     $validated = $request->validate([
    //         'client_name' => 'sometimes|string|max:255',
    //         'location' => 'sometimes|string|max:255',
    //         'cleaning_type' => 'sometimes|string|max:255',
    //         'visit_date' => 'sometimes|date',
    //         'contact' => 'sometimes|string|max:255',
    //         'relance_date' => 'sometimes|date',
    //     ]);

    //     $visit->update($validated);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Visite mise à jour avec succès',
    //         'data' => $visit,
    //     ]);
    // }

    // /**
    //  * Supprimer une visite
    //  */
    // public function destroy(Request $request, $id)
    // {
    //     $visit = CommercialVisit::where('id', $id)
    //                            ->where('user_id', $request->user()->id)
    //                            ->first();

    //     if (!$visit) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Visite non trouvée',
    //         ], 404);
    //     }

    //     $visit->delete();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Visite supprimée avec succès',
    //     ]);
    // }
}

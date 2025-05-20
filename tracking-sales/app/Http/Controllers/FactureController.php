<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Facture;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FacturesExport;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use thiagoalessio\TesseractOCR\TesseractOCR; // or another OCR library




class FactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $factures = Facture::latest()->paginate(10);
        return view('factures.index', compact('factures'));
    }
    public function export()
{
    return Excel::download(new FacturesExport, 'factures.xlsx', \Maatwebsite\Excel\Excel::XLSX);
}


public function exportPDF()
{
    $factures = Facture::all();
    $pdf = PDF::loadView('factures.pdf', compact('factures'));
    return $pdf->download('factures.pdf');
}




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('factures.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'prix' => 'required|numeric',
            'departement' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:spc,cheque,virement',
            'societe' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('factures');
        }

        Facture::create($data);

        return redirect()->route('factures.index')->with('success', 'Facture added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facture $facture)
    {
        return view('factures.create', compact('facture'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facture $facture)
    {
        $data = $request->validate([
            'prix' => 'required|numeric',
            'departement' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:spc,cheque,virement',
            'societe' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('file')) {
            if ($facture->file_path) {
                Storage::delete($facture->file_path);
            }
            $data['file_path'] = $request->file('file')->store('factures');
        }

        $facture->update($data);

        return redirect()->route('factures.index')->with('success', 'Facture updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facture $facture)
    {
        if ($facture->file_path) {
            Storage::delete($facture->file_path);
        }
        $facture->delete();
        return redirect()->route('factures.index')->with('success', 'Facture deleted.');
    }


    public function scan(Request $request)
    {
        $request->validate([
            'scan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        // Store temporarily
        $path = $request->file('scan')->store('temp-scans');
        $fullPath = storage_path('app/' . $path);

        try {
            // Pour les PDF, convertir en image d'abord
            $isPdf = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION)) === 'pdf';
            $imagePath = $fullPath;

            if ($isPdf) {
                // Utiliser Imagick ou une autre bibliothèque pour convertir PDF en image
                // Exemple simplifié - dans un cas réel, vous devriez traiter toutes les pages
                $imagePath = storage_path('app/temp-scans/pdf_page.png');

                // Utiliser poppler-utils (pré-installé)
                $command = "pdftoppm -png -singlefile '{$fullPath}' " . storage_path('app/temp-scans/pdf_page');
                exec($command);

                // Si le fichier n'existe pas, utiliser une autre approche
                if (!file_exists($imagePath)) {
                    throw new \Exception("Impossible de convertir le PDF en image");
                }
            }

            // Exécuter l'OCR
            $ocr = new TesseractOCR($imagePath);

            // Configurer Tesseract pour de meilleurs résultats
            $ocr->lang('fra'); // Pour le français
            $ocr->psm(6);      // Assume un bloc de texte uniforme

            // Exécuter l'OCR
            $text = $ocr->run();

            // Analyser le texte pour extraire les informations pertinentes
            // Amélioration des expressions régulières pour mieux correspondre aux factures
            preg_match('/(?:prix|montant|total)\s*:?\s*([\d\s,.]+)(?:\s*[€\$])?/i', $text, $mPrix);
            preg_match('/(?:date|émission)\s*:?\s*(\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4}|\d{4}[\/-]\d{1,2}[\/-]\d{1,2})/i', $text, $mDate);
            preg_match('/(?:département|departement|service)\s*:?\s*([a-zÀ-ÿ\s]+)/i', $text, $mDep);
            preg_match('/(?:société|societe|fournisseur|client)\s*:?\s*([a-zÀ-ÿ\s]+)/i', $text, $mSoc);
            preg_match('/(?:type|paiement|règlement)\s*:?\s*(spc|cheque|virement|carte)/i', $text, $mType);

            // Nettoyer les valeurs extraites
            $prix = isset($mPrix[1]) ? preg_replace('/[^\d,.]/', '', $mPrix[1]) : '';
            $prix = str_replace(',', '.', $prix); // Convertir virgule en point pour format numérique

            $date = isset($mDate[1]) ? trim($mDate[1]) : '';
            $departement = isset($mDep[1]) ? trim($mDep[1]) : '';
            $societe = isset($mSoc[1]) ? trim($mSoc[1]) : '';
            $type = isset($mType[1]) ? strtolower(trim($mType[1])) : '';

            // Vérifier si le type correspond à l'une des valeurs autorisées
            if ($type && !in_array($type, ['spc', 'cheque', 'virement'])) {
                // Essayer de faire correspondre à l'une des valeurs autorisées
                if (strpos($type, 'cheq') !== false) {
                    $type = 'cheque';
                } elseif (strpos($type, 'vir') !== false) {
                    $type = 'virement';
                } else {
                    $type = 'spc'; // Valeur par défaut
                }
            }

            // Construire la réponse
            $fields = [
                'prix' => $prix,
                'date' => $date,
                'departement' => $departement,
                'societe' => $societe,
                'type' => $type,
            ];

            // Nettoyer les fichiers temporaires
            if ($isPdf && $imagePath !== $fullPath) {
                @unlink($imagePath);
            }

            return response()->json([
                'success' => true,
                'fields' => $fields,
                'debug' => [
                    'extracted_text' => $text,
                    'matches' => [
                        'prix' => $mPrix,
                        'date' => $mDate,
                        'departement' => $mDep,
                        'societe' => $mSoc,
                        'type' => $mType
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'extraction OCR: ' . $e->getMessage()
            ]);
        } finally {
            // Nettoyer le fichier temporaire
            Storage::delete($path);
        }
    }
}

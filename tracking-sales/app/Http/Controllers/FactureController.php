<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Facture;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FacturesExport;
use Barryvdh\DomPDF\Facade\Pdf as PDF;




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
}

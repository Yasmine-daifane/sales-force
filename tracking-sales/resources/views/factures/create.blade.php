@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ isset($facture) ? 'Edit' : 'Add' }} Facture</h2>

   <div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Scanner une facture</h5>
    </div>
    <div class="card-body">
        <p>Téléchargez une facture scannée pour remplir automatiquement le formulaire</p>

        <div class="input-group">
            <input type="file" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
            <button type="button" class="btn btn-primary">Scanner et extraire</button>
        </div>
    </div>
</div>

    <form action="{{ isset($facture) ? route('factures.update', $facture) : route('factures.store') }}" method="POST" enctype="multipart/form-data" id="factureForm">
        @csrf
        @if(isset($facture)) @method('PUT') @endif

        <div class="form-group mb-3">
            <label>Prix</label>
            <input type="number" step="0.01" name="prix" id="prix" class="form-control" value="{{ old('prix', $facture->prix ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label>Département</label>
            <input type="text" name="departement" id="departement" class="form-control" value="{{ old('departement', $facture->departement ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label>Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $facture->date ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label>Type</label>
            <select name="type" id="type" class="form-control">
                @foreach(['spc', 'cheque', 'virement'] as $t)
                    <option value="{{ $t }}" {{ (old('type', $facture->type ?? '') == $t) ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Société</label>
            <input type="text" name="societe" id="societe" class="form-control" list="societes" value="{{ old('societe', $facture->societe ?? '') }}">
            <datalist id="societes">
                <option value="Saturne">
                <option value="Regmant">
                <option value="Lange">
                <option value="LND">
            </datalist>
        </div>

        <div class="form-group mb-3">
            <label>Document (pour archivage)</label>
            <input type="file" name="file" class="form-control">
            @if(isset($facture) && $facture->file_path)
                <small><a href="{{ asset('storage/' . $facture->file_path) }}" target="_blank">Current File</a></small>
            @endif
            <small class="form-text text-muted">Ce fichier sera enregistré avec la facture mais ne sera pas utilisé pour l'OCR.</small>
        </div>

        <button type="submit" class="btn btn-success">{{ isset($facture) ? 'Update' : 'Save' }}</button>
    </form>
</div>

@endsection



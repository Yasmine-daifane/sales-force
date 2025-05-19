@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ isset($facture) ? 'Edit' : 'Add' }} Facture</h2>

    <form action="{{ isset($facture) ? route('factures.update', $facture) : route('factures.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($facture)) @method('PUT') @endif

        <div class="form-group mb-3">
            <label>Prix</label>
            <input type="number" step="0.01" name="prix" class="form-control" value="{{ old('prix', $facture->prix ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label>Département</label>
            <input type="text" name="departement" class="form-control" value="{{ old('departement', $facture->departement ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ old('date', $facture->date ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label>Type</label>
            <select name="type" class="form-control">
                @foreach(['spc', 'cheque', 'virement'] as $t)
                    <option value="{{ $t }}" {{ (old('type', $facture->type ?? '') == $t) ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Société</label>
            <input type="text" name="societe" class="form-control" list="societes" value="{{ old('societe', $facture->societe ?? '') }}">
            <datalist id="societes">
                <option value="Saturne">
                <option value="Regmant">
                <option value="Lange">
                <option value="LND">
            </datalist>
        </div>

        <div class="form-group mb-3">
            <label>Scanned Document (optional)</label>
            <input type="file" name="file" class="form-control">
            @if(isset($facture) && $facture->file_path)
                <small><a href="{{ asset('storage/' . $facture->file_path) }}" target="_blank">Current File</a></small>
            @endif
        </div>

        <button type="submit" class="btn btn-success">{{ isset($facture) ? 'Update' : 'Save' }}</button>
    </form>
</div>
@endsection

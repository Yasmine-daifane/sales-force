@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Mes Visites</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('visits.create') }}" class="btn btn-primary mb-3">
        Ajouter une visite
    </a>

    @if($visits->isEmpty())
        <p>Aucune visite pour le moment.</p>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date visite</th>
                <th>Client</th>
                <th>Type de nettoyage</th>
                <th>Lieu</th>
                <th>Contact</th>
                <th>Date relance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
            <tr>
                <td>{{ \Carbon\Carbon::parse($visit->visit_date)->format('Y-m-d') }}</td>
                <td>{{ $visit->client_name }}</td>
                <td>{{ $visit->cleaning_type }}</td>
                <td>{{ $visit->location }}</td>
                <td>{{ $visit->contact }}</td>
                <td>{{ \Carbon\Carbon::parse($visit->relance_date)->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection

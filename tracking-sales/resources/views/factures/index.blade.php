<!-- resources/views/factures/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Toutes les Factures</h2>

    <div class="row mb-3">
        <div class="col-md-8">
            <a href="{{ route('factures.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une Facture
            </a>

            <a href="{{ route('factures.export') }}" class="btn btn-success" id="export-excel-btn">
                <i class="fas fa-file-excel"></i> Exporter en Excel
            </a>

            <a href="{{ route('factures.exportPDF') }}" class="btn btn-danger" id="export-pdf-btn" data-url="{{ route('factures.exportPDF') }}">
                <i class="fas fa-file-pdf"></i> Exporter en PDF
            </a>
        </div>

        {{-- <div class="col-md-4">
            <form action="{{ route('factures.index') }}" method="GET" class="form-inline float-end">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div> --}}
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header bg-light">
            Liste des factures ({{ $factures->total() }})
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Prix</th>
                            <th>Département</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Société</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($factures as $facture)
                            <tr>
                                <td>{{ number_format($facture->prix, 2, ',', ' ') }} €</td>
                                <td>{{ $facture->departement }}</td>
                                <td>{{ \Carbon\Carbon::parse($facture->date)->format('d/m/Y') }}</td>
                                <td>
                                    @if($facture->type == 'spc')
                                        <span class="badge bg-primary">SPC</span>
                                    @elseif($facture->type == 'cheque')
                                        <span class="badge bg-success">Chèque</span>
                                    @elseif($facture->type == 'virement')
                                        <span class="badge bg-info">Virement</span>
                                    @endif
                                </td>
                                <td>{{ $facture->societe }}</td>
                                <td>
                                    @if($facture->file_path)
                                        <a href="{{ asset('storage/' . $facture->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-file"></i> Voir
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                <td>
                                        <a href="{{ route('factures.edit', $facture) }}" class="btn btn-sm btn-warning">Edit</a>

                                       <form action="{{ route('factures.destroy', $facture) }}" method="POST"  class="d-inline delete-form">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger delete-btn">Delete</button>
                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3">Aucune facture trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $factures->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/factures.js') }}"></script>
@endpush




@endsection

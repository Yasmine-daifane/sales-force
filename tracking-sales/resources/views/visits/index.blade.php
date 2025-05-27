@extends("layouts.app")

@section("content")
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-12"> {{-- Use full width or adjust as needed --}}
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white"> {{-- Changed color for distinction --}}
                    <h4 class="mb-0">{{ $title }}</h4>
                </div>
                <div class="card-body">
                    {{-- Search Form --}}
                    <form method="GET" action="{{ route("visits.index") }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ $search ?? '' }}">
                            <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
                        </div>
                    </form>

                    @can("create", App\Models\CommercialVisit::class) {{-- Example using Policy --}}
                        <a href="{{ route("visits.create") }}" class="btn btn-primary mb-3">
                            Ajouter une visite
                        </a>
                    @endcan

                    {{-- Visits Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    @if(Auth::user() && strtolower(Auth::user()->role ?? '') === 'admin')
                                        <th>Commercial</th>
                                    @endif
                                    <th>Client</th>
                                    <th>Lieu</th>
                                    <th>Type Nettoyage</th>
                                    <th>Date Visite</th>
                                    <th>Contact</th>
                                    <th>Date Relance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($visits as $visit)
                                    <tr>
                                        @if(Auth::user() && strtolower(Auth::user()->role ?? '') === 'admin')
                                            <td>{{ $visit->user->name ?? "N/A" }}</td>
                                        @endif
                                        <td>{{ $visit->client_name }}</td>
                                        <td>{{ $visit->location }}</td>
                                        <td>{{ $visit->cleaning_type }}</td>
                                        <td>{{ $visit->visit_date ? \Carbon\Carbon::parse($visit->visit_date)->format("d/m/Y") : "N/A" }}</td>
                                        <td>{{ $visit->contact }}</td>
                                        <td>{{ $visit->relance_date ? \Carbon\Carbon::parse($visit->relance_date)->format("d/m/Y") : "N/A" }}</td>
                                        <td>

                                            {{-- <a href="{{ route("visits.edit", $visit->id) }}" class="btn btn-sm btn-warning">Edit</a> --}}

                                            <form action="{{ route("visits.destroy", $visit->id) }}" method="POST" class="d-inline" onsubmit="return confirm("Êtes-vous sûr de vouloir supprimer cette visite ?");">
                                                @csrf
                                                @method("DELETE")
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Adjust colspan based on whether the Commercial column is shown --}}
                                        <td colspan="{{ (Auth::user() && strtolower(Auth::user()->role ?? '') === 'admin') ? 8 : 7 }}" class="text-center">Aucune visite trouvée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Links - Placed correctly after the table, inside card-body --}}
                       <div class="d-flex justify-content-end mt-4">
                            {{ $visits->links() }} {{-- Bootstrap 5 pagination styling is applied by default in Laravel 9+ --}}
                        </div>

                </div> {{-- End card-body --}}
            </div> {{-- End card --}}
        </div> {{-- End col --}}
    </div> {{-- End row --}}
</div> {{-- End container --}}
@endsection


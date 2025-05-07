@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Liste des Visites Commerciales</h2>

  <div class="mb-3">
 <div class="card-body">
    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
    </form>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Commercial</th>
                <th>Client</th>
                <th>Location</th>
                <th>Type</th>
                <th>Visit Date</th>
                <th>Contact</th>
                <th>Relance Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visits as $visit)
                <tr>
                 <td>{{ $visit->user->name ?? 'N/A' }}</td> <!-- Commercial name -->
                    <td>{{ $visit->client_name }}</td>
                    <td>{{ $visit->location }}</td>
                    <td>{{ $visit->cleaning_type }}</td>
                    <td>{{ $visit->visit_date }}</td>
                    <td>{{ $visit->contact }}</td>
                    <td>{{ $visit->relance_date }}</td>
                    <td>
                        <a href="{{ route('visits.edit', $visit->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('visits.destroy', $visit->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No data found</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $visits->links() }}
</div>



    {{ $visits->appends(['search' => request('search')])->links() }}
</div>
@endsection

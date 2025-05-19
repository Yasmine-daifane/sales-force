@extends('layouts.app')

@section('content')
<div class="container">
    <h2>All Factures</h2>
    <a href="{{ route('factures.create') }}" class="btn btn-primary mb-3">Add Facture</a>

  <button id="export-excel-btn" class="btn btn-success mb-3">Exporter en Excel</button>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
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
            @foreach($factures as $facture)
                <tr>
                    <td>{{ $facture->prix }}</td>
                    <td>{{ $facture->departement }}</td>
                    <td>{{ $facture->date }}</td>
                    <td>{{ ucfirst($facture->type) }}</td>
                    <td>{{ $facture->societe }}</td>
                    <td>
                        @if($facture->file_path)
                            <a href="{{ asset('storage/' . $facture->file_path) }}" target="_blank">View</a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('factures.edit', $facture) }}" class="btn btn-sm btn-warning">Edit</a>
                       <form action="{{ route('factures.destroy', $facture) }}" method="POST" style="display:inline-block;" class="delete-form">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-danger delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $factures->links() }}
</div>



<script>
    // Excel export with success SweetAlert
    document.getElementById('export-excel-btn').addEventListener('click', function () {
        const link = document.createElement('a');
        link.href = "{{ route('factures.export') }}";
        link.download = 'factures.xlsx';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        Swal.fire({
            icon: 'success',
            title: 'Téléchargement lancé !',
            text: 'Le fichier Excel est en cours de téléchargement.',
            timer: 2000,
            showConfirmButton: false
        });
    });

    // SweetAlert delete confirmation
    document.querySelectorAll('.delete-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            Swal.fire({
                title: 'Supprimer cette facture ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        });
    });
</script>
@endsection

<!-- resources/views/factures/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Liste des Factures</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .header {
            margin-bottom: 30px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des Factures</h1>
        <p>Date d'exportation: {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Prix (€)</th>
                <th>Département</th>
                <th>Date</th>
                <th>Type</th>
                <th>Société</th>
            </tr>
        </thead>
        <tbody>
            @forelse($factures as $facture)
                <tr>
                    <td>{{ number_format($facture->prix, 2, ',', ' ') }}</td>
                    <td>{{ $facture->departement }}</td>
                    <td>{{ \Carbon\Carbon::parse($facture->date)->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($facture->type) }}</td>
                    <td>{{ $facture->societe }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Aucune facture trouvée</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Ce document a été généré automatiquement le {{ date('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>

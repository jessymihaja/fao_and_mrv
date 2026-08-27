<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport {{ $rapport->titre }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; color: #333; }
        h1 { color: #1e3a8a; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f3f4f6; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Imprimer / Sauvegarder en PDF</button>
    </div>

    <h1>{{ $rapport->titre }}</h1>
    <p>Année : {{ $rapport->annee ?? 'Toutes' }}</p>

    <h3>Résumé Financier</h3>
    <table>
        <tr>
            <th>Projets Totaux</th>
            <td>{{ $rapport->contenu['resume']['total_projets'] ?? 0 }}</td>
        </tr>
        <tr>
            <th>Budget Total Approuvé</th>
            <td>{{ number_format($rapport->contenu['resume']['budget_total_approuve'] ?? 0, 2) }} USD</td>
        </tr>
    </table>
</body>
</html>
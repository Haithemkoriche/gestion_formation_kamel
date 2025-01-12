<!DOCTYPE html>
<html>
<head>
    <title>Liste des Employés</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des Employés</h1>
        <p>Date d'export: {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Matricule</th>
                <th>Grade</th>
                <th>Unité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employes as $employe)
            <tr>
                <td>{{ $employe->nom }}</td>
                <td>{{ $employe->prenom }}</td>
                <td>{{ $employe->matricule }}</td>
                <td>{{ $employe->grade }}</td>
                <td>{{ $employe->unite }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

@extends('layouts.app')

@section('title', 'Liste des Sous-Équipements')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div><h1>Sous-Équipements</h1></div>
    <a href="{{ route('sous-equipements.create') }}" class="btn btn-gmao-primary">Nouveau</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-gmao">
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Désignation</th>
                    <th>Équipement Parent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sousEquipements as $se)
                <tr>
                    <td>{{ $se->identifiant }}</td>
                    <td>{{ $se->designation }}</td>
                    <td>{{ $se->equipement->code ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('sous-equipements.show', $se) }}" class="btn btn-action btn-view" title="Voir"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('sous-equipements.edit', $se) }}" class="btn btn-action btn-edit" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Historique des statuts')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Historique des statuts</h1>
        <p class="breadcrumb">GMAO > Installations > Historique</p>
    </div>
    <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary">Retour aux installations</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-gmao table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Installation</th>
                    <th>Ancien statut</th>
                    <th>Nouveau statut</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historiques as $historique)
                    <tr>
                        <td>{{ $historique->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($historique->installation)
                                <a href="{{ route('installations.show', $historique->installation) }}">
                                    {{ $historique->installation->code_installation }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $historique->ancien_statut ?: '-' }}</td>
                        <td><strong>{{ $historique->nouveau_statut }}</strong></td>
                        <td>{{ $historique->commentaire }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Aucun historique enregistre.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

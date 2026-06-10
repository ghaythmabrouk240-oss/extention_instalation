@extends('layouts.app')

@section('title', 'Calendrier Installations')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1>Calendrier des Installations</h1>
        <p class="breadcrumb">GMAO > Installations > Calendrier</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('installations.index') }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-list me-2"></i>Liste
        </a>
        <a href="{{ route('installations.calendar', ['month' => now()->format('Y-m')]) }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-calendar-day me-2"></i>Aujourd'hui
        </a>
        <a href="{{ route('installations.calendar', ['month' => $previousMonth]) }}" class="btn btn-gmao-secondary">
            <i class="fa-solid fa-chevron-left me-2"></i>Mois precedent
        </a>
        <a href="{{ route('installations.calendar', ['month' => $nextMonth]) }}" class="btn btn-gmao-secondary">
            Mois suivant<i class="fa-solid fa-chevron-right ms-2"></i>
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h3 class="mb-0">{{ $month->translatedFormat('F Y') }}</h3>
            <div class="calendar-legend d-flex flex-wrap gap-3">
                <span><i class="calendar-dot calendar-dot-planned"></i> Installation planifiee</span>
                <span><i class="calendar-dot calendar-dot-actual"></i> Installation reelle</span>
                <span><i class="calendar-dot calendar-dot-intervention"></i> Intervention / changement statut</span>
            </div>
        </div>
        <p class="text-muted small mb-0 mt-2">
            Les installations et interventions sont enregistrees automatiquement depuis la planification et l'historique des statuts.
            Ce mois : {{ $monthStats['installations'] }} debut(s) d'installation, {{ $monthStats['interventions'] }} intervention(s).
        </p>
    </div>
</div>

<div class="card calendar-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table calendar-grid mb-0">
                <thead>
                    <tr>
                        <th>Lundi</th>
                        <th>Mardi</th>
                        <th>Mercredi</th>
                        <th>Jeudi</th>
                        <th>Vendredi</th>
                        <th>Samedi</th>
                        <th>Dimanche</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weeks as $week)
                        <tr>
                            @foreach($week as $day)
                                <td class="calendar-day {{ !$day['isCurrentMonth'] ? 'calendar-day-outside' : '' }} {{ $day['isToday'] ? 'calendar-day-today' : '' }}">
                                    <div class="calendar-day-header">
                                        <span class="calendar-day-number">{{ $day['date']->format('d') }}</span>
                                        @if(!empty($day['events']))
                                            <span class="calendar-day-count">{{ count($day['events']) }}</span>
                                        @endif
                                    </div>

                                    <div class="calendar-day-events">
                                        @foreach($day['events'] as $event)
                                            @if($event['type'] === 'installation_planned')
                                                @php($installation = $event['installation'])
                                                <a href="{{ route('installations.show', $installation) }}" class="calendar-event calendar-event-planned" title="{{ $installation->nom }}">
                                                    <strong>{{ $installation->code_installation }}</strong>
                                                    <span>{{ $installation->nom }}</span>
                                                    @if($event['isStart'] && $installation->calendar_note)
                                                        <em>{{ $installation->calendar_note }}</em>
                                                    @endif
                                                </a>
                                            @elseif($event['type'] === 'installation_actual')
                                                @php($installation = $event['installation'])
                                                <a href="{{ route('installations.show', $installation) }}" class="calendar-event calendar-event-actual" title="Installation reelle">
                                                    <strong>{{ $installation->code_installation }}</strong>
                                                    <span>Reel : {{ $installation->statut }}</span>
                                                </a>
                                            @elseif($event['type'] === 'intervention')
                                                @php($intervention = $event['intervention'])
                                                <a href="{{ route('installations.show', $intervention->installation) }}" class="calendar-event calendar-event-intervention" title="{{ $intervention->commentaire }}">
                                                    <strong>{{ $intervention->installation->code_installation ?? 'INST' }}</strong>
                                                    <span>{{ $intervention->ancien_statut }} &rarr; {{ $intervention->nouveau_statut }}</span>
                                                    @if($intervention->commentaire)
                                                        <em>{{ $intervention->commentaire }}</em>
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

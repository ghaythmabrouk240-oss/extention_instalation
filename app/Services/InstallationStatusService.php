<?php

namespace App\Services;

use App\Models\Installation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class InstallationStatusService
{
    public const BROUILLON = 'Brouillon';
    public const EN_VALIDATION = 'En validation';
    public const INSTALLE = 'Installé';
    public const OPERATIONNEL = 'Opérationnel';
    public const EN_MAINTENANCE = 'En maintenance';
    public const INDISPONIBLE = 'Temporairement indisponible';
    public const ARCHIVE = 'Archivé';

    public static function statuses(): array
    {
        return [
            self::BROUILLON,
            self::EN_VALIDATION,
            self::INSTALLE,
            self::OPERATIONNEL,
            self::EN_MAINTENANCE,
            self::INDISPONIBLE,
            self::ARCHIVE,
        ];
    }

    public function assertCanTransition(
        Installation $installation,
        string $newStatus,
        User $user,
        array $pendingProfileData = []
    ): void {
        $oldStatus = $installation->statut;

        if ($oldStatus === $newStatus) {
            return;
        }

        if (! in_array($newStatus, self::statuses(), true)) {
            throw ValidationException::withMessages([
                'statut' => 'Statut non reconnu pour le POC.',
            ]);
        }

        if ($newStatus === self::ARCHIVE && ! $user->isAdmin()) {
            throw ValidationException::withMessages([
                'statut' => 'Seul un administrateur peut archiver une installation.',
            ]);
        }

        if (! in_array($newStatus, $this->allowedNextStatuses($oldStatus, $user), true)) {
            throw ValidationException::withMessages([
                'statut' => "Transition non autorisée: {$oldStatus} vers {$newStatus}.",
            ]);
        }

        if (
            in_array($newStatus, [self::EN_VALIDATION, self::INSTALLE, self::OPERATIONNEL], true)
            && ! $this->hasMinimumProfileData($installation, $pendingProfileData)
        ) {
            throw ValidationException::withMessages([
                'statut' => 'Le profil minimum doit être complété avant cette transition.',
            ]);
        }
    }

    public function allowedNextStatuses(string $status, User $user): array
    {
        $activeArchive = $user->isAdmin() ? [self::ARCHIVE] : [];

        return match ($status) {
            self::BROUILLON => [self::EN_VALIDATION, ...$activeArchive],
            self::EN_VALIDATION => [self::INSTALLE, self::BROUILLON, ...$activeArchive],
            self::INSTALLE => [self::OPERATIONNEL, self::EN_MAINTENANCE, self::INDISPONIBLE, ...$activeArchive],
            self::OPERATIONNEL => [self::EN_MAINTENANCE, self::INDISPONIBLE, ...$activeArchive],
            self::EN_MAINTENANCE => [self::OPERATIONNEL, self::INDISPONIBLE, ...$activeArchive],
            self::INDISPONIBLE => [self::EN_MAINTENANCE, self::OPERATIONNEL, ...$activeArchive],
            self::ARCHIVE => [],
            default => [self::BROUILLON],
        };
    }

    public function hasMinimumProfileData(Installation $installation, array $pendingProfileData = []): bool
    {
        if ($installation->type_profil !== Installation::TYPE_IRM) {
            return true;
        }

        $profile = array_filter([
            'champ_magnetique' => $pendingProfileData['champ_magnetique'] ?? $installation->profilIrm?->champ_magnetique,
            'blindage' => $pendingProfileData['blindage'] ?? $installation->profilIrm?->blindage,
            'batiment' => $pendingProfileData['batiment'] ?? $installation->profilIrm?->batiment,
            'zone' => $pendingProfileData['zone'] ?? $installation->profilIrm?->zone,
        ], fn ($value) => filled($value));

        return count($profile) === 4;
    }
}

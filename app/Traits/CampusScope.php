<?php

namespace App\Traits;

use App\Models\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait CampusScope
{
    /**
     * Récupère l'ID du campus actif
     * Pour super admin: campus sélectionné ou null (tous)
     * Pour admin: son campus_id
     */
    public static function getActiveCampusId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Super admin peut changer de campus
        if ($user->isSuperAdmin()) {
            $selectedCampus = Session::get('selected_campus_id');
            // Si 'all' ou null, retourne null pour voir tous les campus
            if ($selectedCampus === 'all' || $selectedCampus === null) {
                return null;
            }
            return (int) $selectedCampus;
        }

        // Admin normal: son campus
        return $user->campus_id;
    }

    /**
     * Vérifie si on affiche tous les campus (mode super admin global)
     */
    public static function isViewingAllCampuses(): bool
    {
        $user = Auth::user();

        if (!$user || !$user->isSuperAdmin()) {
            return false;
        }

        $selectedCampus = Session::get('selected_campus_id');
        return $selectedCampus === 'all' || $selectedCampus === null;
    }

    /**
     * Définit le campus actif pour le super admin
     */
    public static function setActiveCampus($campusId): void
    {
        $user = Auth::user();

        if ($user && $user->isSuperAdmin()) {
            Session::put('selected_campus_id', $campusId);
        }
    }

    /**
     * Récupère le nom du campus actif
     */
    public static function getActiveCampusName(): string
    {
        $campusId = self::getActiveCampusId();

        if ($campusId === null) {
            return 'Tous les campus';
        }

        $campus = Campus::find($campusId);
        return $campus ? $campus->nomCampus : 'Campus inconnu';
    }

    /**
     * Récupère tous les campus (pour le sélecteur)
     */
    public static function getAllCampuses()
    {
        return Campus::orderBy('nomCampus')->get();
    }
}

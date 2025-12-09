<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs
     */
    public function getAllUser()
    {
        $campusId = Auth::user()->campus_id;
        $isSuperAdmin = Auth::user()->isSuperAdmin();

        // Si super admin (campus 3), voir tous les users
        // Sinon voir seulement les users du même campus
        $users = User::with('campus')
            ->when(!$isSuperAdmin, function ($query) use ($campusId) {
                return $query->where('campus_id', $campusId);
            })
            ->orderBy('name')
            ->get();

        return view('pages.admin', compact('users'));
    }

    /**
     * Récupère le nom du campus
     */
    public static function getCampusName($campus_id): string
    {
        $campus = Campus::find($campus_id);
        return $campus?->nomCampus ?? 'N/A';
    }

    /**
     * Liste tous les campus
     */
    public static function getAllCampus()
    {
        return Campus::orderBy('nomCampus')->get();
    }

    /**
     * Active un utilisateur
     */
    public function activerUser($id)
    {
        $user = User::findOrFail($id);

        // Vérifier les permissions
        if (!$this->canManageUser($user)) {
            return back()->withStatus('Vous n\'avez pas les permissions nécessaires');
        }

        $user->update(['etat' => User::ETAT_ACTIF]);

        return back()->withStatus('Compte activé avec succès');
    }

    /**
     * Supprime un utilisateur
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Ne pas supprimer son propre compte
        if ($user->id === Auth::id()) {
            return back()->withStatus('Vous ne pouvez pas supprimer votre propre compte');
        }

        // Vérifier les permissions
        if (!$this->canManageUser($user)) {
            return back()->withStatus('Vous n\'avez pas les permissions nécessaires');
        }

        $user->delete();

        return back()->withStatus('Compte supprimé avec succès');
    }

    /**
     * Définit un utilisateur comme admin
     */
    public function defineAdmin($id)
    {
        $user = User::findOrFail($id);

        // Vérifier les permissions
        if (!$this->canManageUser($user)) {
            return back()->withStatus('Vous n\'avez pas les permissions nécessaires');
        }

        // Ne pas modifier son propre rôle
        if ($user->id === Auth::id()) {
            return back()->withStatus('Vous ne pouvez pas modifier votre propre rôle');
        }

        $user->update(['role' => User::ROLE_ADMIN]);

        return back()->withStatus('Compte défini comme administrateur');
    }

    /**
     * Désactive un utilisateur
     */
    public function desactiverUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->withStatus('Vous ne pouvez pas désactiver votre propre compte');
        }

        if (!$this->canManageUser($user)) {
            return back()->withStatus('Vous n\'avez pas les permissions nécessaires');
        }

        $user->update(['etat' => User::ETAT_INACTIF]);

        return back()->withStatus('Compte désactivé avec succès');
    }

    /**
     * Vérifie si l'utilisateur connecté peut gérer un autre utilisateur
     */
    protected function canManageUser(User $user): bool
    {
        $currentUser = Auth::user();

        // Super admin peut tout faire
        if ($currentUser->isSuperAdmin()) {
            return true;
        }

        // Admin ne peut gérer que les users de son campus
        if ($currentUser->isAdmin()) {
            return $user->campus_id === $currentUser->campus_id;
        }

        return false;
    }
}

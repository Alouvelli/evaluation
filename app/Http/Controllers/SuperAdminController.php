<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Classes;
use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\Evaluation;
use App\Models\Professeur;
use App\Models\User;
use App\Traits\CampusScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SuperAdminController extends Controller
{
    use CampusScope;

    /**
     * Dashboard super admin avec vue globale
     */
    public function dashboard(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $campuses = Campus::withCount(['classes', 'etudiants', 'cours', 'users'])
            ->orderBy('nomCampus')
            ->get();

        // Statistiques globales
        $globalStats = [
            'totalCampus' => $campuses->count(),
            'totalProfesseurs' => Professeur::count(),
            'totalEtudiants' => Etudiant::count(),
            'totalCours' => Cours::count(),
            'totalClasses' => Classes::count(),
            'totalEvaluations' => Evaluation::count(),
            'totalUsers' => User::count(),
        ];

        // Statistiques par campus
        $campusStats = [];
        foreach ($campuses as $campus) {
            // Cours actifs
            $coursActifs = Cours::where('campus_id', $campus->id)
                ->where('etat', 1)
                ->count();

            // Étudiants ayant évalué
            $etudiantsEvalues = Etudiant::where('campus_id', $campus->id)
                ->where('statut', Etudiant::STATUT_A_EVALUE)
                ->count();

            $totalEtudiants = Etudiant::where('campus_id', $campus->id)->count();

            // Taux de participation
            $tauxParticipation = $totalEtudiants > 0
                ? round(($etudiantsEvalues / $totalEtudiants) * 100)
                : 0;

            // Moyenne générale du campus
            $moyenneGenerale = DB::table('evaluations')
                ->join('cours', 'evaluations.id_cours', '=', 'cours.id_cours')
                ->where('cours.campus_id', $campus->id)
                ->where('cours.etat', 1)
                ->avg('evaluations.note');

            $campusStats[] = [
                'campus' => $campus,
                'coursActifs' => $coursActifs,
                'etudiantsEvalues' => $etudiantsEvalues,
                'totalEtudiants' => $totalEtudiants,
                'tauxParticipation' => $tauxParticipation,
                'moyenneGenerale' => $moyenneGenerale ? round($moyenneGenerale) : 0,
            ];
        }

        // Top 5 professeurs globaux (meilleure note)
        $topProfesseurs = DB::table('evaluations')
            ->join('professeurs', 'evaluations.id_professeur', '=', 'professeurs.id')
            ->join('cours', 'evaluations.id_cours', '=', 'cours.id_cours')
            ->join('campus', 'cours.campus_id', '=', 'campus.id')
            ->where('cours.etat', 1)
            ->select(
                'professeurs.id',
                'professeurs.full_name',
                'campus.nomCampus',
                DB::raw('ROUND(AVG(evaluations.note)) as moyenne')
            )
            ->groupBy('professeurs.id', 'professeurs.full_name', 'campus.nomCampus')
            ->orderByDesc('moyenne')
            ->limit(5)
            ->get();

        return view('super-admin.dashboard', compact(
            'campuses',
            'globalStats',
            'campusStats',
            'topProfesseurs'
        ));
    }

    /**
     * Change le campus actif pour la session
     */
    public function switchCampus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $campusId = $request->input('campus_id');

        if ($campusId === 'all') {
            Session::put('selected_campus_id', 'all');
            $message = 'Vue globale activée (tous les campus)';
        } else {
            $campus = Campus::findOrFail($campusId);
            Session::put('selected_campus_id', $campus->id);
            $message = 'Campus changé : ' . $campus->nomCampus;
        }

        return back()->with('status', $message);
    }

    /**
     * Gestion des utilisateurs (tous campus)
     */
    public function users(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $users = User::with('campus')
            ->orderBy('name')
            ->get();

        $campuses = Campus::orderBy('nomCampus')->get();

        return view('super-admin.users', compact('users', 'campuses'));
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function createUser(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:user,admin,super_admin',
            'campus_id' => 'required_unless:role,super_admin|exists:campus,id',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email existe déjà.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 6 caractères.',
            'role.required' => 'Le rôle est obligatoire.',
            'campus_id.required_unless' => 'Le campus est obligatoire pour ce rôle.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'campus_id' => $request->role === 'super_admin' ? null : $request->campus_id,
            'etat' => User::ETAT_ACTIF,
        ]);

        return redirect()->route('super-admin.users')
            ->with('status', 'Utilisateur créé avec succès.');
    }

    /**
     * Modifier un utilisateur
     */
    public function updateUser(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:user,admin,super_admin',
            'campus_id' => 'required_unless:role,super_admin|exists:campus,id',
            'etat' => 'required|in:0,1',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'campus_id' => $request->role === 'super_admin' ? null : $request->campus_id,
            'etat' => $request->etat,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->route('super-admin.users')
            ->with('status', 'Utilisateur modifié avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function deleteUser($id): \Illuminate\Http\RedirectResponse
    {
        $user = User::findOrFail($id);

        // Ne pas supprimer son propre compte
        if ($user->id === auth()->id()) {
            return back()->with('status', 'Erreur : Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('super-admin.users')
            ->with('status', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Gestion des campus
     */
    public function campuses(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $campuses = Campus::withCount(['classes', 'etudiants', 'cours', 'users'])
            ->orderBy('nomCampus')
            ->get();

        return view('super-admin.campuses', compact('campuses'));
    }

    /**
     * Créer un nouveau campus
     */
    public function createCampus(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'nomCampus' => 'required|string|max:255|unique:campus,nomCampus',
        ], [
            'nomCampus.required' => 'Le nom du campus est obligatoire.',
            'nomCampus.unique' => 'Ce campus existe déjà.',
        ]);

        Campus::create([
            'nomCampus' => $request->nomCampus,
        ]);

        return redirect()->route('super-admin.campuses')
            ->with('status', 'Campus créé avec succès.');
    }

    /**
     * Modifier un campus
     */
    public function updateCampus(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $campus = Campus::findOrFail($id);

        $request->validate([
            'nomCampus' => 'required|string|max:255|unique:campus,nomCampus,' . $id,
        ]);

        $campus->update([
            'nomCampus' => $request->nomCampus,
        ]);

        return redirect()->route('super-admin.campuses')
            ->with('status', 'Campus modifié avec succès.');
    }

    /**
     * Supprimer un campus
     */
    public function deleteCampus($id): \Illuminate\Http\RedirectResponse
    {
        $campus = Campus::findOrFail($id);

        // Vérifier les dépendances
        if ($campus->classes()->exists()) {
            return back()->with('status', 'Erreur : Ce campus contient des classes.');
        }
        if ($campus->users()->exists()) {
            return back()->with('status', 'Erreur : Ce campus a des utilisateurs associés.');
        }

        $campus->delete();

        return redirect()->route('super-admin.campuses')
            ->with('status', 'Campus supprimé avec succès.');
    }

    /**
     * Rapport comparatif entre campus
     */
    public function comparatif(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $campuses = Campus::orderBy('nomCampus')->get();

        $comparatifData = collect(); // Utiliser une collection

        foreach ($campuses as $campus) {
            // Moyenne des professeurs
            $moyenneProfs = DB::table('evaluations')
                ->join('cours', 'evaluations.id_cours', '=', 'cours.id_cours')
                ->where('cours.campus_id', $campus->id)
                ->where('cours.etat', 1)
                ->avg('evaluations.note');

            // Taux participation
            $totalEtudiants = Etudiant::where('campus_id', $campus->id)->count();
            $etudiantsEvalues = Etudiant::where('campus_id', $campus->id)
                ->where('statut', Etudiant::STATUT_A_EVALUE)
                ->count();

            $tauxParticipation = $totalEtudiants > 0
                ? round(($etudiantsEvalues / $totalEtudiants) * 100)
                : 0;

            // Répartition des notes
            $tresSatisfaisant = 0;
            $satisfaisant = 0;
            $peuSatisfaisant = 0;

            $profsCampus = DB::table('evaluations')
                ->join('cours', 'evaluations.id_cours', '=', 'cours.id_cours')
                ->where('cours.campus_id', $campus->id)
                ->where('cours.etat', 1)
                ->select('cours.id_professeur', DB::raw('AVG(evaluations.note) as moyenne'))
                ->groupBy('cours.id_professeur')
                ->get();

            foreach ($profsCampus as $prof) {
                if ($prof->moyenne > 85) $tresSatisfaisant++;
                elseif ($prof->moyenne >= 65) $satisfaisant++;
                else $peuSatisfaisant++;
            }

            $comparatifData->push([
                'campus' => $campus,
                'campusName' => $campus->nomCampus,
                'moyenneGenerale' => $moyenneProfs ? round($moyenneProfs) : 0,
                'tauxParticipation' => $tauxParticipation,
                'totalProfs' => $profsCampus->count(),
                'tresSatisfaisant' => $tresSatisfaisant,
                'satisfaisant' => $satisfaisant,
                'peuSatisfaisant' => $peuSatisfaisant,
            ]);
        }

        return view('super-admin.comparatif', compact('comparatifData'));
    }
}

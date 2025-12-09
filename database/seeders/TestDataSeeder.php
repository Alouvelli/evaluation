<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Campus
        DB::table('campus')->insert([
            ['id' => 1, 'nomCampus' => 'Campus Dakar'],
            ['id' => 2, 'nomCampus' => 'Campus Thiès'],
            ['id' => 3, 'nomCampus' => 'Campus Principal'], // Super Admin
        ]);

        // Années académiques
        DB::table('annee_academique')->insert([
            ['id' => 1, 'annee1' => 2023, 'annee2' => 2024],
            ['id' => 2, 'annee1' => 2024, 'annee2' => 2025],
        ]);

        // Niveaux
        DB::table('niveaux')->insert([
            ['id_niveau' => 1, 'libelle_niveau' => 'Licence 1', 'created_at' => now(), 'updated_at' => now()],
            ['id_niveau' => 2, 'libelle_niveau' => 'Licence 2', 'created_at' => now(), 'updated_at' => now()],
            ['id_niveau' => 3, 'libelle_niveau' => 'Licence 3', 'created_at' => now(), 'updated_at' => now()],
            ['id_niveau' => 4, 'libelle_niveau' => 'Master 1', 'created_at' => now(), 'updated_at' => now()],
            ['id_niveau' => 5, 'libelle_niveau' => 'Master 2', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Users (admin)
        DB::table('users')->insert([
            [
                'name' => 'Admin Dakar',
                'email' => 'admin@dakar.sn',
                'password' => Hash::make('password'),
                'etat' => 1,
                'role' => 'admin',
                'campus_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@isi.sn',
                'password' => Hash::make('password'),
                'etat' => 1,
                'role' => 'super_admin',
                'campus_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Classes
        DB::table('classes')->insert([
            ['id' => 1, 'libelle' => 'GL-A', 'campus_id' => 1, 'id_niveau' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'libelle' => 'GL-B', 'campus_id' => 1, 'id_niveau' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'libelle' => 'RS-A', 'campus_id' => 1, 'id_niveau' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'libelle' => 'GL-A', 'campus_id' => 1, 'id_niveau' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Professeurs
        DB::table('professeurs')->insert([
            ['id' => 1, 'full_name' => 'Dr. Amadou DIALLO', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'full_name' => 'Prof. Fatou NDIAYE', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'full_name' => 'M. Moussa FALL', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'full_name' => 'Dr. Ibrahima SARR', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Cours
        DB::table('cours')->insert([
            [
                'libelle_cours' => 'Algorithmique',
                'id_classe' => 1,
                'id_professeur' => 1,
                'semestre' => 1,
                'campus_id' => 1,
                'annee_id' => 2,
                'etat' => 1, // Actif
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle_cours' => 'Base de données',
                'id_classe' => 1,
                'id_professeur' => 2,
                'semestre' => 1,
                'campus_id' => 1,
                'annee_id' => 2,
                'etat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle_cours' => 'Programmation Web',
                'id_classe' => 1,
                'id_professeur' => 3,
                'semestre' => 1,
                'campus_id' => 1,
                'annee_id' => 2,
                'etat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'libelle_cours' => 'Réseaux',
                'id_classe' => 3,
                'id_professeur' => 4,
                'semestre' => 1,
                'campus_id' => 1,
                'annee_id' => 2,
                'etat' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Questions d'évaluation
        DB::table('questions')->insert([
            ['libelle' => 'Le professeur est-il ponctuel ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Le professeur explique-t-il clairement le cours ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Le professeur est-il disponible pour les étudiants ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Le contenu du cours correspond-il au programme ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Les supports de cours sont-ils de qualité ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Le professeur encourage-t-il la participation ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Les exercices/TP sont-ils adaptés au cours ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Le professeur respecte-t-il le volume horaire ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'L\'évaluation est-elle cohérente avec le cours ?', 'created_at' => now(), 'updated_at' => now()],
            ['libelle' => 'Recommanderiez-vous ce professeur ?', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Étudiants de test (classe GL-A L1)
        $etudiants = [];
        for ($i = 1; $i <= 10; $i++) {
            $etudiants[] = [
                'matricule' => 202400100 + $i,
                'statut' => 0, // Peut évaluer
                'id_classe' => 1,
                'campus_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('etudiants')->insert($etudiants);

        $this->command->info('✅ Données de test insérées avec succès !');
        $this->command->info('');
        $this->command->info('📧 Comptes de test :');
        $this->command->info('   - admin@dakar.sn / password (Admin campus Dakar)');
        $this->command->info('   - superadmin@isi.sn / password (Super Admin)');
        $this->command->info('');
        $this->command->info('🎓 Matricules étudiants : 202400101 à 202400110 (format: 202-40-0101)');
    }
}

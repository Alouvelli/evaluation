<?php

namespace App\Imports;

use App\Models\Etudiant;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class EtudiantImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    protected int $classeId;
    protected int $campusId;
    protected int $importCount = 0;
    protected int $skipCount = 0;

    public function __construct(int $classeId, int $campusId)
    {
        $this->classeId = $classeId;
        $this->campusId = $campusId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $matricule = $this->findMatricule($row->toArray());

            if (!$matricule) {
                $this->skipCount++;
                continue;
            }

            // Vérifier si l'étudiant existe déjà
            $existingEtudiant = Etudiant::where('matricule', $matricule)->first();

            if ($existingEtudiant) {
                $existingEtudiant->update([
                    'id_classe' => $this->classeId,
                    'campus_id' => $this->campusId,
                    'statut' => Etudiant::STATUT_INACTIF,
                ]);
                $this->skipCount++;
                Log::info('Étudiant existant mis à jour: ' . $matricule);
            } else {
                Etudiant::create([
                    'matricule' => $matricule,
                    'statut' => Etudiant::STATUT_INACTIF,
                    'id_classe' => $this->classeId,
                    'campus_id' => $this->campusId,
                ]);
                $this->importCount++;
                Log::info('Nouvel étudiant créé et sauvegardé: ' . $matricule);
            }
        }
    }

    protected function findMatricule(array $row): ?string
    {
        foreach ($row as $value) {
            if (!empty($value)) {
                $cleaned = trim((string) $value);
                if (!empty($cleaned)) {
                    // Enlever tirets et espaces du matricule
                    return preg_replace('/[\s\-]/', '', $cleaned);
                }
            }
        }
        return null;
    }

    public function getImportCount(): int
    {
        return $this->importCount;
    }

    public function getSkipCount(): int
    {
        return $this->skipCount;
    }
}

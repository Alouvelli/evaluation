<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Évaluation des enseignants - ISI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #667eea;
            --primary-dark: #5a67d8;
            --success: #10b981;
            --grade-a: #10b981;
            --grade-b: #f59e0b;
            --grade-c: #f97316;
            --grade-d: #ef4444;
            --bg: #f8fafc;
            --card: #ffffff;
            --border: #e2e8f0;
            --text: #0f172a;
            --text-light: #64748b;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 1rem;
            color: var(--text);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: var(--shadow-lg);
        }

        .logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        h1 {
            color: white;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .student-info {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            display: inline-block;
            color: white;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        /* Légende */
        .legend {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 1.5rem 0;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            box-shadow: var(--shadow);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 6px;
        }

        /* Barre de progression */
        .progress-wrapper {
            margin-bottom: 1.5rem;
        }

        .progress-bar {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            height: 10px;
            overflow: hidden;
        }

        .progress-fill {
            background: linear-gradient(90deg, var(--success) 0%, #34d399 100%);
            height: 100%;
            width: 0%;
            transition: width 0.4s ease;
            border-radius: 9999px;
        }

        .progress-text {
            color: white;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            margin-top: 0.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Tableau */
        .table-wrapper {
            background: var(--card);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            overflow-x: auto;
            animation: fadeInUp 0.6s ease;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 900px;
        }

        thead {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        th {
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
            padding: 1rem 0.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.15);
            white-space: nowrap;
        }

        th:first-child {
            text-align: left;
            padding-left: 1rem;
            min-width: 200px;
            position: sticky;
            left: 0;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            z-index: 11;
        }

        tbody tr {
            transition: background 0.2s ease;
        }

        tbody tr:hover {
            background: #f1f5f9;
        }

        tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        tbody tr:nth-child(even):hover {
            background: #f1f5f9;
        }

        td {
            padding: 0.4rem;
            border-bottom: 1px solid var(--border);
            border-right: 1px solid var(--border);
            text-align: center;
            vertical-align: middle;
        }

        td:first-child {
            font-weight: 600;
            color: var(--primary);
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            position: sticky;
            left: 0;
            background: inherit;
            z-index: 5;
            min-width: 200px;
        }

        tbody tr:nth-child(odd) td:first-child {
            background: white;
        }

        tbody tr:nth-child(even) td:first-child {
            background: #fafbfc;
        }

        tbody tr:hover td:first-child {
            background: #f1f5f9;
        }

        .course-name {
            font-size: 0.75rem;
            color: var(--text-light);
            font-weight: 400;
            display: block;
            margin-top: 2px;
        }

        /* Cellules de notation */
        .cell-options {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            padding: 2px;
        }

        .cell-options.chosen {
            grid-template-columns: 1fr;
        }

        .choice {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            color: white;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }

        .choice::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .choice:hover::before {
            width: 80px;
            height: 80px;
        }

        .choice[data-value="100"] {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .choice[data-value="75"] {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .choice[data-value="50"] {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }

        .choice[data-value="25"] {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .choice:hover {
            transform: translateY(-2px) scale(1.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .choice.selected {
            transform: scale(1.15);
            border-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.4), 0 6px 16px rgba(0, 0, 0, 0.25);
            animation: selectPulse 0.5s ease;
        }

        .choice.hidden {
            display: none;
        }

        @keyframes selectPulse {
            0%, 100% { transform: scale(1.15); }
            50% { transform: scale(1.2); }
        }

        /* Section commentaires */
        .comments-section {
            background: var(--card);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            padding: 1.5rem;
            margin-top: 1.5rem;
            animation: fadeInUp 0.6s ease 0.1s backwards;
        }

        .comments-section h3 {
            color: var(--primary);
            font-size: 1.1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .comment-group {
            margin-bottom: 1rem;
        }

        .comment-group label {
            display: block;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .comment-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 80px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .comment-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        .comment-group textarea::placeholder {
            color: var(--text-light);
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            animation: fadeInUp 0.6s ease 0.2s backwards;
        }

        button {
            padding: 1rem 2.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        #submitBtn {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            color: white;
        }

        #submitBtn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        #submitBtn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        #resetBtn {
            background: white;
            color: var(--text);
            border: 2px solid var(--border);
        }

        #resetBtn:hover {
            background: var(--bg);
            border-color: var(--text-light);
        }

        /* Loading overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        .loading-spinner {
            background: white;
            padding: 2rem 3rem;
            border-radius: 16px;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            h1 { font-size: 1.8rem; }
            th, td { font-size: 0.8rem; }
            .choice { width: 32px; height: 32px; font-size: 0.8rem; }
        }

        @media (max-width: 768px) {
            body { padding: 1rem 0.5rem; }
            h1 { font-size: 1.5rem; }
            .table-wrapper { border-radius: 10px; }
            .choice { width: 28px; height: 28px; font-size: 0.75rem; }
            button { padding: 0.8rem 1.5rem; font-size: 0.9rem; }
            .legend-item { padding: 0.4rem 0.75rem; font-size: 0.75rem; }
            .comments-section { padding: 1rem; }
        }

        /* Alert messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-weight: 500;
            animation: fadeInDown 0.4s ease;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="{{ asset('dist/img/isi_logo.png') }}" alt="ISI Logo" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2280%22>🎓</text></svg>'">
        </div>
        <h1>📋 Évaluation des Enseignements</h1>
        <p class="subtitle">Évaluez chaque critère pour tous vos cours</p>
        <div class="student-info">
            🎓 Matricule : <strong>{{ $matricule_formate ?? '---' }}</strong> | Classe : <strong>{{ $classe->libelle ?? '---' }}</strong>
        </div>

        <!-- Légende -->
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"></div>
                <span>A - Trés Satisfait (100%)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"></div>
                <span>B - Satisfait (75%)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);"></div>
                <span>C - Plus ou moins Satisfait (50%)</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);"></div>
                <span>D - Peu Satisfait (25%)</span>
            </div>
        </div>

        <!-- Barre de progression -->
        <div class="progress-wrapper">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-text" id="progressText">0% complété (0 / 0)</div>
        </div>
    </header>

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="alert alert-error">
            <strong>⚠️ Erreur :</strong> {{ $errors->first() }}
        </div>
    @endif

    <!-- Formulaire d'évaluation -->
    <form id="evaluationForm" action="{{ route('evaluation.store') }}" method="POST">
        @csrf
        <input type="hidden" name="matricule" value="{{ $matricule }}">
        <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">

        <!-- Tableau d'évaluation -->
        <div class="table-wrapper">
            <table id="evaluationTable">
                <thead>
                <tr>
                    <th>Cours / Professeur</th>
                    @foreach($questions as $question)
                        <th title="{{ $question->libelle }}">
                            {{ Str::limit($question->libelle, 15) }}
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($cours as $c)
                    <tr data-cours-id="{{ $c->id_cours }}" data-prof-id="{{ $c->id_professeur }}">
                        <td>
                            <strong>{{ $c->professeur->full_name }}</strong>
                            <span class="course-name">{{ $c->libelle_cours }}</span>
                        </td>
                        @foreach($questions as $question)
                            <td>
                                <div class="cell-options"
                                     data-cours="{{ $c->id_cours }}"
                                     data-prof="{{ $c->id_professeur }}"
                                     data-question="{{ $question->idQ }}">
                                    <div class="choice" data-value="100" title="très Satisfait">A</div>
                                    <div class="choice" data-value="75" title="Satisfait">B</div>
                                    <div class="choice" data-value="50" title="Plus ou moins Satisfait">C</div>
                                    <div class="choice" data-value="25" title="Peu Satisfait">D</div>
                                </div>
                                <!-- Input caché pour stocker la valeur -->
                                <input type="hidden"
                                       name="evaluations[{{ $c->id_cours }}][{{ $c->id_professeur }}][{{ $question->idQ }}]"
                                       class="eval-input"
                                       data-cell="{{ $c->id_cours }}-{{ $question->idQ }}"
                                       required>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Section commentaires -->
        <div class="comments-section">
            <h3>💬 Commentaires (facultatif)</h3>
            <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 1rem;">
                Vous pouvez laisser un commentaire pour chaque professeur. Ces commentaires resterons anonymes.
            </p>

            @foreach($cours as $c)
                <div class="comment-group">
                    <label for="comment_{{ $c->id_cours }}">
                        {{ $c->professeur->full_name }} - {{ $c->libelle_cours }}
                    </label>
                    <textarea
                        name="commentaires[{{ $c->id_cours }}][{{ $c->id_professeur }}]"
                        id="comment_{{ $c->id_cours }}"
                        placeholder="Votre commentaire sur ce cours et ce professeur (facultatif)..."
                        maxlength="1000"></textarea>
                </div>
            @endforeach
        </div>

        <!-- Boutons d'action -->
        <div class="actions">
            <button type="button" id="resetBtn">
                🔄 Réinitialiser
            </button>
            <button type="submit" id="submitBtn" disabled>
                ✓ Soumettre l'évaluation
            </button>
        </div>
    </form>
</div>

<!-- Loading overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
        <div class="spinner"></div>
        <p>Enregistrement en cours...</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalCells = document.querySelectorAll('.cell-options').length;
        let selectedCount = 0;

        // Mettre à jour la progression
        function updateProgress() {
            const percentage = totalCells > 0 ? Math.round((selectedCount / totalCells) * 100) : 0;
            document.getElementById('progressFill').style.width = percentage + '%';
            document.getElementById('progressText').textContent =
                `${percentage}% complété (${selectedCount} / ${totalCells})`;

            // Activer/désactiver le bouton de soumission
            document.getElementById('submitBtn').disabled = selectedCount < totalCells;
        }

        // Initialiser la progression
        updateProgress();

        // Gestion des clics sur les choix
        document.addEventListener('click', function(e) {
            if (!e.target.classList.contains('choice')) return;

            const cell = e.target.closest('.cell-options');
            const wasSelected = cell.querySelector('.selected');
            const allChoices = cell.querySelectorAll('.choice');

            // Incrémenter le compteur si c'est une nouvelle sélection
            if (!wasSelected) {
                selectedCount++;
            }

            // Masquer les autres choix et sélectionner celui-ci
            allChoices.forEach(choice => {
                choice.classList.remove('selected');
                if (choice !== e.target) {
                    choice.classList.add('hidden');
                }
            });

            cell.classList.add('chosen');
            e.target.classList.add('selected');
            e.target.classList.remove('hidden');

            // Mettre à jour l'input caché
            const coursId = cell.dataset.cours;
            const profId = cell.dataset.prof;
            const questionId = cell.dataset.question;
            const value = e.target.dataset.value;

            const input = cell.parentElement.querySelector('.eval-input');
            if (input) {
                input.value = value;
            }

            updateProgress();
        });

        // Permettre de changer de choix en cliquant sur la cellule sélectionnée
        document.addEventListener('dblclick', function(e) {
            if (!e.target.classList.contains('choice') || !e.target.classList.contains('selected')) return;

            const cell = e.target.closest('.cell-options');
            const allChoices = cell.querySelectorAll('.choice');

            // Réafficher tous les choix
            allChoices.forEach(choice => {
                choice.classList.remove('selected', 'hidden');
            });
            cell.classList.remove('chosen');

            // Réinitialiser l'input
            const input = cell.parentElement.querySelector('.eval-input');
            if (input) {
                input.value = '';
            }

            selectedCount--;
            updateProgress();
        });

        // Bouton de réinitialisation
        document.getElementById('resetBtn').addEventListener('click', function() {
            if (!confirm('⚠️ Êtes-vous sûr de vouloir réinitialiser toutes les évaluations ?')) return;

            // Réinitialiser tous les choix
            document.querySelectorAll('.choice').forEach(choice => {
                choice.classList.remove('selected', 'hidden');
            });

            document.querySelectorAll('.cell-options').forEach(cell => {
                cell.classList.remove('chosen');
            });

            // Réinitialiser tous les inputs
            document.querySelectorAll('.eval-input').forEach(input => {
                input.value = '';
            });

            // Réinitialiser les commentaires
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.value = '';
            });

            selectedCount = 0;
            updateProgress();
        });

        // Soumission du formulaire
        document.getElementById('evaluationForm').addEventListener('submit', function(e) {
            if (selectedCount < totalCells) {
                e.preventDefault();
                alert(`⚠️ Veuillez compléter toutes les évaluations.\n\nProgression : ${selectedCount}/${totalCells}`);
                return false;
            }

            // Afficher le loading
            document.getElementById('loadingOverlay').classList.add('show');
            document.getElementById('submitBtn').disabled = true;
        });

        // Sauvegarde automatique dans localStorage
        function saveToLocalStorage() {
            const data = {
                selections: {},
                comments: {}
            };

            document.querySelectorAll('.cell-options').forEach(cell => {
                const selected = cell.querySelector('.selected');
                if (selected) {
                    const key = `${cell.dataset.cours}-${cell.dataset.prof}-${cell.dataset.question}`;
                    data.selections[key] = selected.dataset.value;
                }
            });

            document.querySelectorAll('textarea').forEach(textarea => {
                if (textarea.value) {
                    data.comments[textarea.id] = textarea.value;
                }
            });

            localStorage.setItem('evaluation_draft_{{ $matricule }}', JSON.stringify(data));
        }

        // Restaurer depuis localStorage
        function restoreFromLocalStorage() {
            const saved = localStorage.getItem('evaluation_draft_{{ $matricule }}');
            if (!saved) return;

            try {
                const data = JSON.parse(saved);

                // Restaurer les sélections
                if (data.selections) {
                    Object.keys(data.selections).forEach(key => {
                        const [coursId, profId, questionId] = key.split('-');
                        const cell = document.querySelector(
                            `.cell-options[data-cours="${coursId}"][data-prof="${profId}"][data-question="${questionId}"]`
                        );
                        if (cell) {
                            const choice = cell.querySelector(`.choice[data-value="${data.selections[key]}"]`);
                            if (choice) {
                                choice.click();
                            }
                        }
                    });
                }

                // Restaurer les commentaires
                if (data.comments) {
                    Object.keys(data.comments).forEach(id => {
                        const textarea = document.getElementById(id);
                        if (textarea) {
                            textarea.value = data.comments[id];
                        }
                    });
                }
            } catch (e) {
                console.error('Erreur lors de la restauration:', e);
            }
        }

        // Sauvegarder à chaque changement
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('choice')) {
                setTimeout(saveToLocalStorage, 100);
            }
        });

        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', saveToLocalStorage);
        });

        // Restaurer au chargement
        restoreFromLocalStorage();

        // Nettoyer localStorage après soumission réussie
        document.getElementById('evaluationForm').addEventListener('submit', function() {
            setTimeout(() => {
                localStorage.removeItem('evaluation_draft_{{ $matricule }}');
            }, 1000);
        });

        // Info-bulle sur double-clic
        console.log('💡 Astuce: Double-cliquez sur une note pour la modifier');
    });
</script>
</body>
</html>

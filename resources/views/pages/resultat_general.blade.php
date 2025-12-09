@extends('layouts.admin')

@section('title', 'Résultat Général')

@section('content')
    <!-- Stats globales -->
    <div class="stats-row stats-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $profs }}</h3>
                <p>Professeurs</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $classes->count() }}</h3>
                <p>Classes</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $questions->count() }}</h3>
                <p>Questions</p>
            </div>
        </div>
    </div>

    <!-- Sélecteur de classe -->
    <div class="card mb-4">
        <div class="card-header">
            <h3><i class="fas fa-filter"></i> Sélectionner une classe</h3>
        </div>
        <div class="card-body">
            <select id="classeSelect" class="classe-select">
                <option value="">-- Choisir une classe --</option>
                @foreach($classes as $classe)
                    <option value="{{ $classe->id }}">
                        {{ $classe->niveau->libelle_niveau ?? '' }} {{ $classe->libelle }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Loading -->
    <div id="loadingZone" class="loading-zone" style="display: none;">
        <div class="loading-box">
            <div class="spinner"></div>
            <p>Chargement des résultats...</p>
        </div>
    </div>

    <!-- Message si pas de données -->
    <div id="emptyZone" style="display: none;">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Aucun cours actif</strong> - Cette classe n'a pas de cours actif ou pas encore d'évaluations.
        </div>
    </div>

    <!-- Zone de résultats -->
    <div id="resultatsZone" style="display: none;">
        <!-- Stats classe -->
        <div class="stats-row stats-mini" id="classeStats">
            <div class="stat-card mini">
                <div class="stat-icon info">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-content">
                    <h3 id="nbCours">0</h3>
                    <p>Cours</p>
                </div>
            </div>
            <div class="stat-card mini">
                <div class="stat-icon success">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-content">
                    <h3 id="nbEtudiants">0</h3>
                    <p>Étudiants</p>
                </div>
            </div>
            <div class="stat-card mini">
                <div class="stat-icon warning">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-content">
                    <h3 id="tauxParticipation">0%</h3>
                    <p>Participation</p>
                </div>
            </div>
            <div class="stat-card mini">
                <div class="stat-icon primary">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-content">
                    <h3 id="moyenneClasse">0%</h3>
                    <p>Moyenne</p>
                </div>
            </div>
        </div>

        <!-- Tableau des résultats -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-table"></i> Résultats - <span id="classeNom"></span></h3>
            </div>
            <div class="card-body">
                <!-- Vue Desktop -->
                <div class="table-scroll-wrapper" id="desktopTable">
                    <table class="table-results">
                        <thead>
                        <tr>
                            <th class="col-fixed">Professeur</th>
                            <th>Cours</th>
                            @foreach($questions as $q)
                                <th class="col-note" title="{{ $q->libelle }}">Q{{ $q->idQ }}</th>
                            @endforeach
                            <th class="col-note">Moy.</th>
                        </tr>
                        </thead>
                        <tbody id="resultatsBody"></tbody>
                    </table>
                </div>

                <!-- Vue Mobile -->
                <div class="mobile-cards" id="mobileCards"></div>
            </div>
        </div>

        <!-- Graphique -->
        <div class="card mt-4">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Graphique des moyennes</h3>
            </div>
            <div class="card-body">
                <canvas id="chartMoyennes" height="100"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classeSelect = document.getElementById('classeSelect');
            const resultatsZone = document.getElementById('resultatsZone');
            const loadingZone = document.getElementById('loadingZone');
            const emptyZone = document.getElementById('emptyZone');
            const resultatsBody = document.getElementById('resultatsBody');
            const mobileCards = document.getElementById('mobileCards');
            const nbQuestions = {{ $questions->count() }};

            let chart = null;

            classeSelect.addEventListener('change', function() {
                const classeId = this.value;

                if (!classeId) {
                    resultatsZone.style.display = 'none';
                    emptyZone.style.display = 'none';
                    return;
                }

                loadingZone.style.display = 'block';
                resultatsZone.style.display = 'none';
                emptyZone.style.display = 'none';

                fetch(`/api/resultat-classe/${classeId}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingZone.style.display = 'none';

                        if (data.cours.length === 0) {
                            emptyZone.style.display = 'block';
                            return;
                        }

                        resultatsZone.style.display = 'block';

                        // Stats
                        document.getElementById('classeNom').textContent = data.classe.niveau + ' ' + data.classe.libelle;
                        document.getElementById('nbCours').textContent = data.cours.length;
                        document.getElementById('nbEtudiants').textContent = data.classe.nbEtudiants;
                        document.getElementById('tauxParticipation').textContent = data.classe.tauxParticipation + '%';

                        // Moyenne classe
                        let totalMoyenne = 0;
                        data.cours.forEach(c => {
                            const moy = c.notes.reduce((a, b) => a + b, 0) / c.notes.length;
                            totalMoyenne += moy;
                        });
                        const moyenneClasse = Math.round(totalMoyenne / data.cours.length);
                        document.getElementById('moyenneClasse').textContent = moyenneClasse + '%';

                        // Tableau desktop
                        resultatsBody.innerHTML = '';
                        data.cours.forEach(c => {
                            const moy = Math.round(c.notes.reduce((a, b) => a + b, 0) / c.notes.length);
                            let moyClass = moy >= 75 ? 'moy-success' : (moy >= 50 ? 'moy-warning' : 'moy-danger');

                            let row = `<tr><td class="col-fixed"><strong>${c.professeur}</strong></td><td>${c.libelle}</td>`;
                            c.notes.forEach(note => {
                                let cellClass = note >= 75 ? 'cell-good' : (note >= 50 ? 'cell-warning' : 'cell-danger');
                                row += `<td class="col-note ${note > 0 ? cellClass : ''}">${note > 0 ? note + '%' : '-'}</td>`;
                            });
                            row += `<td class="col-note"><span class="moyenne-badge ${moyClass}">${moy}%</span></td></tr>`;
                            resultatsBody.innerHTML += row;
                        });

                        // Cards mobile
                        mobileCards.innerHTML = '';
                        data.cours.forEach(c => {
                            const moy = Math.round(c.notes.reduce((a, b) => a + b, 0) / c.notes.length);
                            let moyClass = moy >= 75 ? 'moy-success' : (moy >= 50 ? 'moy-warning' : 'moy-danger');

                            let notesHtml = '';
                            c.notes.forEach((note, i) => {
                                let cellClass = note >= 75 ? 'cell-good' : (note >= 50 ? 'cell-warning' : 'cell-danger');
                                notesHtml += `<div class="note-item ${note > 0 ? cellClass : ''}"><span class="note-label">Q${i+1}</span><span class="note-value">${note > 0 ? note + '%' : '-'}</span></div>`;
                            });

                            mobileCards.innerHTML += `
                        <div class="result-card">
                            <div class="result-card-header">
                                <div class="result-info">
                                    <h4>${c.professeur}</h4>
                                    <p>${c.libelle}</p>
                                </div>
                                <span class="moyenne-badge ${moyClass}">${moy}%</span>
                            </div>
                            <div class="result-card-body">
                                <div class="notes-grid">${notesHtml}</div>
                            </div>
                        </div>`;
                        });

                        updateChart(data.cours);
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        loadingZone.style.display = 'none';
                        emptyZone.style.display = 'block';
                    });
            });

            function updateChart(coursData) {
                const ctx = document.getElementById('chartMoyennes').getContext('2d');

                if (chart) chart.destroy();

                const labels = coursData.map(c => c.professeur.split(' ').slice(0, 2).join(' '));
                const moyennes = coursData.map(c => Math.round(c.notes.reduce((a, b) => a + b, 0) / c.notes.length));

                const colors = moyennes.map(m => m >= 75 ? 'rgba(16, 185, 129, 0.8)' : (m >= 50 ? 'rgba(245, 158, 11, 0.8)' : 'rgba(239, 68, 68, 0.8)'));

                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{ label: 'Moyenne (%)', data: moyennes, backgroundColor: colors, borderRadius: 6 }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } },
                            x: { ticks: { maxRotation: 45, minRotation: 45 } }
                        }
                    }
                });
            }
        });
    </script>
@endpush

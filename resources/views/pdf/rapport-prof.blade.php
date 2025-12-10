<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport - {{ $prof->full_name }}</title>
    <style>
        @page {
            margin: 55mm 20mm 35mm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Entête fixe sur chaque page */
        .entete {
            position: fixed;
            top: -55mm;
            left: -20mm;
            width: 210mm;
        }

        /* Pied de page fixe sur chaque page */
        .pied-page {
            position: fixed;
            bottom: -35mm;
            left: -20mm;
            width: 210mm;
        }

        /* Lettre */
        .ref {
            margin-top: 0;
        }
        .destinataire {
            text-align: right;
            margin-top: 5mm;
        }
        .objet {
            margin-top: 10mm;
        }
        .objet-label {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            font-style: italic;
            color: #000;
        }
        .objet-text {
            font-size: 14pt;
            font-style: italic;
            color: #000;
        }
        .lettre-corps {
            margin-top: 8mm;
            line-height: 1.8;
            text-align: justify;
        }
        .signature {
            text-align: right;
            margin-top: 20mm;
            font-style: italic;
            font-weight: bold;
            color: #000;
        }

        /* Note finale - nouvelle page */
        .note-finale-section {
            page-break-before: always;
        }
        .note-finale {
            text-align: center;
            margin: 15mm 0;
            padding: 15px;
            background: #f8fafc;
            border: 2px solid #667eea;
        }
        .note-finale h2 {
            margin: 0;
            font-size: 32pt;
        }
        .note-finale p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12pt;
        }

        /* Légende */
        .legend-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 15px;
            margin-bottom: 8mm;
        }
        .legend-title {
            font-weight: bold;
            color: #667eea;
            margin-bottom: 8px;
            font-size: 11pt;
        }
        .legend-items {
            display: table;
            width: 100%;
        }
        .legend-item {
            display: table-cell;
            width: 33.33%;
            padding: 5px 0;
        }
        .legend-color {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 3px;
            vertical-align: middle;
            margin-right: 5px;
        }
        .legend-color-danger { background: #ef4444; }
        .legend-color-warning { background: #f59e0b; }
        .legend-color-success { background: #10b981; }
        .legend-text {
            font-size: 10pt;
            color: #374151;
        }
        .legend-range {
            font-size: 9pt;
            color: #64748b;
            font-weight: bold;
        }

        /* Sections */
        .section {
            margin-top: 8mm;
        }
        .section-title {
            background: #667eea;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            margin-bottom: 3mm;
        }

        /* Tableaux */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #667eea;
            color: white;
            padding: 8px 5px;
            font-size: 10pt;
            font-weight: bold;
            border: 1px solid #667eea;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px 5px;
            text-align: center;
            font-size: 10pt;
        }
        .cell-label {
            background: #f1f5f9;
            font-weight: bold;
            color: #667eea;
        }
        .cell-good {
            background: #d1fae5;
            color: #065f46;
            font-weight: bold;
        }
        .cell-warning {
            background: #fef3c7;
            color: #92400e;
            font-weight: bold;
        }
        .cell-danger {
            background: #fee2e2;
            color: #991b1b;
            font-weight: bold;
        }
        .cours-name {
            text-align: left;
            font-weight: bold;
        }
    </style>
</head>
<body>
{{-- Entête et pied de page fixes --}}
<img src="{{ public_path('dist/img/entete.png') }}" class="entete" alt="">
<img src="{{ public_path('dist/img/piedDePage.png') }}" class="pied-page" alt="">

{{-- LETTRE --}}
<p class="ref">Ref............</p>

<div class="destinataire">
    <p>A M./Mme l'enseignant(e),</p>
    <p><strong>{{ $prof->full_name }}</strong></p>
</div>

<div class="objet">
    <span class="objet-label">Objet:</span>
    <span class="objet-text"> {{ $objet }}</span>
</div>

<div class="lettre-corps">
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cher distingué enseignant,</p>

    <p>Nous sommes arrivés au terme du {{ $adjectifSemestre }} semestre de l'année académique {{ $evalInfo->annee1 ?? date('Y') }} - {{ $evalInfo->annee2 ?? (date('Y')+1) }}. Nous tenons par cette présente, à vous remercier, au nom du Président Directeur Général et de toute l'équipe pédagogique pour votre engagement au sein du groupe ISI.</p>

    <p>L'évaluation de vos enseignements par les étudiants a révélé un niveau d'appréciation <strong>{{ $appreciation }}</strong>, {{ $avertissement }}</p>

    <p>Nous vous prions de bien vouloir agréer, M/Mme {{ $prof->full_name }}, l'expression de notre considération distinguée.</p>
</div>
<div class="signature">
    La directrice des études
</div>

{{-- NOTE FINALE - Nouvelle page --}}
<div class="note-finale-section">
    <div class="note-finale">
        @php
            if($noteFinale < 65) {
                $noteColor = '#ef4444';
                $noteLabel = 'Peu satisfaisant';
            } elseif($noteFinale <= 85) {
                $noteColor = '#f59e0b';
                $noteLabel = 'Satisfaisant';
            } else {
                $noteColor = '#10b981';
                $noteLabel = 'Très satisfaisant';
            }
        @endphp
        <h2 style="color: {{ $noteColor }};">{{ $noteFinale }}/100</h2>
        <p>Note Finale - <span style="color: {{ $noteColor }}; font-weight: bold;">{{ $noteLabel }}</span></p>
    </div>

    {{-- LÉGENDE --}}
    <div class="legend-box">
        <div class="legend-title">Légende des notes</div>
        <table style="border: none;">
            <tr>
                <td style="border: none; text-align: left; padding: 5px 10px;">
                    <span class="legend-color legend-color-danger"></span>
                    <span class="legend-text">Peu satisfaisant</span>
                    <span class="legend-range">(&lt; 65%)</span>
                </td>
                <td style="border: none; text-align: center; padding: 5px 10px;">
                    <span class="legend-color legend-color-warning"></span>
                    <span class="legend-text">Satisfaisant</span>
                    <span class="legend-range">(65% - 85%)</span>
                </td>
                <td style="border: none; text-align: right; padding: 5px 10px;">
                    <span class="legend-color legend-color-success"></span>
                    <span class="legend-text">Très satisfaisant</span>
                    <span class="legend-range">(&gt; 85%)</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- RÉSULTATS PAR COURS --}}
    <div class="section">
        <div class="section-title">Résultats par Cours</div>
        <table>
            <thead>
            <tr>
                <th style="text-align: left;">Cours / Classe</th>
                @foreach($questions as $q)
                    <th>Q{{ $q->idQ }}</th>
                @endforeach
                <th>Moy.</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cours as $c)
                <tr>
                    <td class="cours-name">
                        {{ $c['libelle'] }}<br>
                        <small style="color: #666;">{{ $c['classe'] }}</small>
                    </td>
                    @foreach($c['notes'] as $note)
                        @php
                            $cellClass = '';
                            if($note > 0) {
                                if($note > 85) $cellClass = 'cell-good';
                                elseif($note >= 65) $cellClass = 'cell-warning';
                                else $cellClass = 'cell-danger';
                            }
                        @endphp
                        <td class="{{ $cellClass }}">{{ $note > 0 ? $note . '%' : '-' }}</td>
                    @endforeach
                    @php
                        $moyClass = '';
                        if($c['moyenne'] > 85) $moyClass = 'cell-good';
                        elseif($c['moyenne'] >= 65) $moyClass = 'cell-warning';
                        else $moyClass = 'cell-danger';
                    @endphp
                    <td class="{{ $moyClass }}">{{ $c['moyenne'] }}%</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- QUESTIONS --}}
    <div class="section">
        <div class="section-title">Questions d'évaluation</div>
        <table>
            <thead>
            <tr>
                <th style="width: 50px;">N°</th>
                <th style="text-align: left;">Libellé</th>
            </tr>
            </thead>
            <tbody>
            @foreach($questions as $q)
                <tr>
                    <td class="cell-label">Q{{ $q->idQ }}</td>
                    <td style="text-align: left;">{{ $q->libelle }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport d'Évaluation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .content {
            background: #f8f9ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .note-box {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .appreciation {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 10px;
        }
        .appreciation.success {
            background: #d1fae5;
            color: #065f46;
        }
        .appreciation.warning {
            background: #fef3c7;
            color: #92400e;
        }
        .appreciation.danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .cta {
            text-align: center;
            margin: 25px 0;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🎓 ISI ENS_EVAL</h1>
        <p>Système de Gestion de la Qualité des Enseignements</p>
    </div>

    <p class="greeting">Cher(e) <strong>{{ $professeur->full_name }}</strong>,</p>

    <p>
        Nous avons le plaisir de vous transmettre votre rapport d'évaluation des enseignements
        pour le <strong>semestre {{ $semestre }}</strong> de l'année académique en cours.
    </p>

    <div class="content">
        <h3 style="margin-top: 0; color: #667eea;">📊 Résumé de votre évaluation</h3>

        <p><strong>Note finale :</strong></p>
        <div class="cta">
            <span class="note-box">{{ $noteFinale }}/100</span>
        </div>

        <p><strong>Appréciation globale :</strong></p>
        <div class="cta">
            @if($noteFinale >= 85)
                <span class="appreciation success">✓ Très satisfaisant</span>
            @elseif($noteFinale >= 65)
                <span class="appreciation warning">● Satisfaisant</span>
            @else
                <span class="appreciation danger">⚠ Peu satisfaisant</span>
            @endif
        </div>
    </div>

    <p>
        Vous trouverez en pièce jointe le rapport détaillé contenant :
    </p>
    <ul>
        <li>Les résultats par cours et par critère d'évaluation</li>
        <li>Les graphiques de performance</li>
        <li>Les commentaires des étudiants (si disponibles)</li>
    </ul>

    @if($noteFinale >= 65)
        <p>
            Nous tenons à vous féliciter pour la qualité de vos enseignements et vous encourageons
            à poursuivre dans cette voie d'excellence.
        </p>
    @else
        <p>
            Nous vous invitons à prendre connaissance des détails de ce rapport et à vous rapprocher
            des responsables de département pour tout accompagnement nécessaire.
        </p>
    @endif

    <div class="footer">
        <p>
            Cet email a été envoyé automatiquement par le système ISI ENS_EVAL.<br>
            Pour toute question, veuillez contacter l'administration.
        </p>
        <p style="color: #999; font-size: 12px;">
            © {{ date('Y') }} ISI - Institut Supérieur d'Informatique
        </p>
    </div>
</div>
</body>
</html>

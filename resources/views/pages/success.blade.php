<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluation Terminée - ISI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            width: 100%;
            max-width: 500px;
            text-align: center;
        }

        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 3rem 2rem;
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(16, 185, 129, 0);
            }
        }

        .success-icon svg {
            width: 50px;
            height: 50px;
            color: white;
        }

        .checkmark {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: drawCheck 0.6s ease 0.3s forwards;
        }

        @keyframes drawCheck {
            to {
                stroke-dashoffset: 0;
            }
        }

        h1 {
            color: #059669;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .message {
            color: #6b7280;
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .message strong {
            color: #374151;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 2rem 0;
        }

        .info-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .info-box p {
            color: #166534;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .footer {
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            pointer-events: none;
            animation: confetti-fall 3s ease-out forwards;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="success-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path class="checkmark" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1>🎉 Évaluation Terminée !</h1>

            <p class="message">
                Merci d'avoir pris le temps d'évaluer vos enseignements.<br>
                <strong>Vos réponses ont été enregistrées avec succès.</strong>
            </p>

            <div class="info-box">
                <p>
                    🔒 Vos réponses sont anonymes et contribuent à améliorer la qualité de l'enseignement.
                </p>
            </div>

            <div class="divider"></div>

            <a href="{{ route('evaluation') }}" class="btn btn-primary">
                <span>🏠</span>
                <span>Retour à l'accueil</span>
            </a>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} ISI - Institut Supérieur d'Informatique</p>
        </div>
    </div>

    <script>
        // Confetti animation
        function createConfetti() {
            const colors = ['#10b981', '#059669', '#667eea', '#764ba2', '#fbbf24', '#f59e0b'];

            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                    confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    document.body.appendChild(confetti);

                    setTimeout(() => confetti.remove(), 4000);
                }, i * 50);
            }
        }

        // Lancer les confettis au chargement
        createConfetti();
    </script>
</body>
</html>

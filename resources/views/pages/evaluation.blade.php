<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Évaluation des Enseignements - ISI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .container {
            width: 100%;
            max-width: 480px;
        }

        .card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
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

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .logo {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .logo img {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }

        .logo i {
            font-size: 2.5rem;
            color: #667eea;
        }

        .card-header h1 {
            color: white;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .card-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }

        .card-body {
            padding: 2.5rem 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.25rem;
            color: #9ca3af;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            font-size: 1.1rem;
            font-family: inherit;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-align: center;
            letter-spacing: 2px;
            font-weight: 600;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
        }

        .form-control::placeholder {
            letter-spacing: normal;
            font-weight: 400;
            color: #9ca3af;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            font-size: 1.05rem;
            font-weight: 600;
            font-family: inherit;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
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

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
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

        .help-text {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .help-text p {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .help-text .format {
            color: #667eea;
            font-weight: 600;
            font-family: monospace;
            font-size: 1rem;
            background: #f3f4f6;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            display: inline-block;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            padding: 0 1rem;
        }

        .admin-link {
            text-align: center;
        }

        .admin-link a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.75rem 1.5rem;
            border: 2px solid #667eea;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .admin-link a:hover {
            background: #667eea;
            color: white;
        }

        .footer {
            text-align: center;
            padding: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }

        /* Animation du bouton au chargement */
        .btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn.loading::after {
            content: '';
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="logo">
                <img src="{{ asset('dist/img/isi_logo.png') }}" alt="ISI Logo" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-graduation-cap\'></i>';">
            </div>
            <h1><i class="fas fa-clipboard-list"></i> Évaluation des Enseignements</h1>
            <p>Institut Supérieur d'Informatique</p>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('check-matricule') }}" method="POST" id="matriculeForm">
                @csrf
                <div class="form-group">
                    <label for="matricule">Entrez votre matricule</label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-card"></i>
                        <input
                            type="text"
                            name="matricule"
                            id="matricule"
                            class="form-control"
                            placeholder="Ex: L1DS&BD-25-4003"
                            value="{{ old('matricule') }}"
                            autocomplete="off"
                            autofocus
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span>Accéder à l'évaluation</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="help-text">
                <p>Entrez votre matricule tel qu'il apparaît sur votre carte d'étudiant</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} ISI - Tous droits réservés</p>
    </div>
</div>

<script>
    // Animation du bouton au submit
    document.getElementById('matriculeForm').addEventListener('submit', function() {
        document.getElementById('submitBtn').classList.add('loading');
    });

    // Convertir en majuscules
    document.getElementById('matricule').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
</body>
</html>

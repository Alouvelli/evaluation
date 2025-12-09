<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ISI Évaluation</title>
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
            max-width: 420px;
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
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .logo img {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }

        .logo i {
            font-size: 2.5rem;
            color: #667eea;
        }

        .card-header h1 {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .card-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .card-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            font-size: 1rem;
            font-family: inherit;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #667eea;
        }

        .forgot-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            padding: 0.875rem;
            font-size: 1rem;
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
            padding: 0.875rem 1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
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

        .eval-link {
            text-align: center;
        }

        .eval-link a {
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

        .eval-link a:hover {
            background: #667eea;
            color: white;
        }

        .footer {
            text-align: center;
            padding: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }

        /* Loading state */
        .btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn.loading::after {
            content: '';
            width: 18px;
            height: 18px;
            border: 2px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
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
            <h1>ISI Évaluation</h1>
            <p>Espace Administrateur</p>
        </div>

        <div class="card-body">
            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            placeholder="votre@email.com"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Se souvenir de moi</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Mot de passe oublié ?</a>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Se connecter</span>
                </button>
            </form>

            <div class="divider">
                <span>ou</span>
            </div>

            <div class="eval-link">
                <a href="{{ route('evaluation') }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Accéder à l'évaluation étudiant</span>
                </a>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Institut Supérieur d'Informatique</p>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function() {
        document.getElementById('submitBtn').classList.add('loading');
    });
</script>
</body>
</html>

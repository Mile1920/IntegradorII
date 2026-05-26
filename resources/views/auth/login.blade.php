<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ingresar | Mina Porco</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, var(--bg-darker) 0%, var(--bg-dark) 50%, var(--primary-blue) 100%);
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            background: rgba(6, 17, 32, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 60px 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7), 0 0 40px rgba(80, 200, 120, 0.3);
            border: 2px solid rgba(80, 200, 120, 0.4);
            position: relative;
            overflow: hidden;
        }

        .login-header {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-bottom: 50px;
        }

        .login-header img {
            width: 100px;
            height: 100px;
            border-radius: 20px;
            border: 5px solid var(--primary-green);
            box-shadow: 0 0 30px rgba(80, 200, 120, 0.8);
        }

        .login-header-text h1 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-green), var(--light-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .login-header-text p {
            margin: 8px 0 0;
            color: rgba(255,255,255,0.9);
            font-size: 16px;
            font-weight: 500;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 18px 50px 18px 22px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(80, 200, 120, 0.4);
            border-radius: 18px;
            color: white !important;
            font-size: 17px;
            transition: all 0.4s;
        }

        .input-group input::placeholder {
            color: rgba(255,255,255,0.7) !important;
            opacity: 1;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary-green);
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 25px rgba(80, 200, 120, 0.6);
            color: white !important;
        }

        /* Asegurar que los campos sean siempre visibles */
        .input-group input {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* EL OJITO PARA MOSTRAR/OCULTAR CONTRASEÑA */
        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.8);
            font-size: 20px;
            transition: 0.3s;
            z-index: 10;
        }

        .toggle-password:hover {
            color: var(--primary-green);
        }

        .btn-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
            color: white;
            border: none;
            border-radius: 18px;
            font-size: 19px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.4s;
            box-shadow: 0 10px 35px rgba(80, 200, 120, 0.6);
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(80, 200, 120, 0.8);
        }

        .error { background: rgba(231, 111, 81, 0.25); color: #ff6b6b; padding: 14px; border-radius: 14px; margin: 18px 0; font-size: 15px; border: 1px solid rgba(231, 111, 81, 0.4); }

        .footer-text { text-align: center; margin-top: 35px; color: rgba(255,255,255,0.8); font-size: 15px; }
        .footer-text a { color: var(--primary-green); text-decoration: none; font-weight: 600; }
        .footer-text a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <img src="{{ asset('img/Logo.png') }}" alt="Mina Porco">
        <div class="login-header-text">
            <h1>Centro Minero Porco</h1>
            <p>Sistema de Gestión Integral</p>
        </div>
    </div>

    <x-auth-session-status class="error" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group">
            <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Correo electrónico">
        </div>
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <div class="input-group">
            <input type="password" name="password" id="password" required placeholder="Contraseña">
            <i class="fas fa-eye toggle-password" id="togglePassword"></i>
        </div>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <button type="submit" class="btn-login">
            Ingresar al Sistema
        </button>
    </form>

    <div class="footer-text">
        <p>¿No tienes cuenta? Contacta al administrador principal</p>
        <p><a href="{{ url('/') }}">← Volver al inicio</a></p>
        <p>© {{ date('Y') }} Mina Porco ⚒️</p>
    </div>
</div>

<!-- SCRIPT DEL OJITO -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        if (togglePassword && password) {
            // Inicialmente oculto (password)
            password.setAttribute('type', 'password');
            togglePassword.classList.remove('fa-eye-slash');
            togglePassword.classList.add('fa-eye');

            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    });
</script>

</body>
</html>
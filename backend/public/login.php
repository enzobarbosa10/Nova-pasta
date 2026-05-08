<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Already authenticated — redirect to dashboard
if (!empty($_SESSION['auth_token']) && !empty($_SESSION['user'])) {
    header('Location: ' . $baseUrl . '/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Acesso — Chapada Diamantina</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --gold:         #C49018;
            --gold-dim:     rgba(196, 144, 24, 0.18);
            --gold-glow:    rgba(196, 144, 24, 0.08);
            --moss:         #3B4A2F;
            --bg:           #0b0e09;
            --surface:      #111610;
            --card:         #161c10;
            --border:       rgba(255,255,255,0.07);
            --border-focus: rgba(196, 144, 24, 0.55);
            --text:         #e8e0d0;
            --muted:        #6b7562;
            --error:        #e05252;
            --error-bg:     rgba(224, 82, 82, 0.08);
            --success:      #52c49a;
            --font-sans:    'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-serif:   'Playfair Display', Georgia, serif;
        }

        html, body {
            height: 100%;
            font-family: var(--font-sans);
            background: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
        }

        /* ── Background ─────────────────────────────────── */
        .login-bg {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(59,74,47,0.35) 0%, transparent 70%),
                radial-gradient(ellipse 50% 40% at 80% 100%, rgba(196,144,24,0.06) 0%, transparent 60%),
                linear-gradient(180deg, #0b0e09 0%, #0f1509 50%, #0a0d07 100%);
            z-index: 0;
        }

        /* Subtle noise texture */
        .login-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            opacity: .4;
            pointer-events: none;
        }

        /* Mountain silhouette */
        .mountain-decor {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 220px;
            z-index: 1;
            pointer-events: none;
        }

        /* ── Wrapper ─────────────────────────────────────── */
        .login-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* ── Card ────────────────────────────────────────── */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 40px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.03),
                0 24px 64px rgba(0,0,0,0.5),
                0 4px 16px rgba(0,0,0,0.3);
            backdrop-filter: blur(8px);
        }

        /* ── Logo ────────────────────────────────────────── */
        .login-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .login-logo svg { flex-shrink: 0; }

        .login-logo-text {
            display: flex;
            flex-direction: column;
        }

        .login-logo-name {
            font-family: var(--font-serif);
            font-size: 15px;
            font-weight: 500;
            color: var(--text);
            letter-spacing: 0.01em;
            line-height: 1.2;
        }

        .login-logo-tagline {
            font-size: 10px;
            font-weight: 400;
            color: var(--muted);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ── Heading ─────────────────────────────────────── */
        .login-heading {
            margin-bottom: 6px;
        }

        .login-heading h1 {
            font-family: var(--font-serif);
            font-size: 22px;
            font-weight: 400;
            color: var(--text);
            letter-spacing: -0.01em;
        }

        .login-subtext {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        /* ── Notice ──────────────────────────────────────── */
        .auth-notice {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--gold-glow);
            border: 1px solid rgba(196,144,24,0.2);
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 24px;
        }

        .auth-notice svg { flex-shrink: 0; color: var(--gold); }

        .auth-notice span {
            font-size: 12px;
            color: rgba(196,144,24,0.9);
            line-height: 1.4;
        }

        /* ── Form ────────────────────────────────────────── */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: rgba(232,224,208,0.6);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 14px;
            font-family: var(--font-sans);
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            -webkit-appearance: none;
        }

        .form-input:-webkit-autofill,
        .form-input:-webkit-autofill:hover,
        .form-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 30px #161c10 inset;
            -webkit-text-fill-color: #e8e0d0;
            transition: background-color 5000s;
        }

        .form-input::placeholder { color: var(--muted); }

        .form-input:focus {
            border-color: var(--border-focus);
            background: rgba(196,144,24,0.04);
            box-shadow: 0 0 0 3px rgba(196,144,24,0.08);
        }

        .form-input.has-icon-right { padding-right: 42px; }

        .input-icon-right {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted);
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color .15s;
        }

        .input-icon-right:hover { color: var(--text); }

        /* ── Checkbox ────────────────────────────────────── */
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 24px;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-row input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 4px;
            background: rgba(255,255,255,0.03);
            cursor: pointer;
            flex-shrink: 0;
            position: relative;
            transition: border-color .15s, background .15s;
        }

        .checkbox-row input[type="checkbox"]:checked {
            background: var(--gold);
            border-color: var(--gold);
        }

        .checkbox-row input[type="checkbox"]:checked::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath d='M2 6l3 3 5-5' stroke='%23000' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/10px no-repeat;
        }

        .checkbox-label {
            font-size: 13px;
            color: rgba(232,224,208,0.65);
        }

        /* ── Submit button ───────────────────────────────── */
        .btn-login {
            width: 100%;
            background: var(--gold);
            color: #0b0e09;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font-sans);
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: opacity .2s, transform .1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover:not(:disabled) { opacity: .9; }
        .btn-login:active:not(:disabled) { transform: scale(0.99); }
        .btn-login:disabled { opacity: .55; cursor: not-allowed; }

        .btn-login .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(0,0,0,0.2);
            border-top-color: #0b0e09;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            display: none;
        }

        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .spinner  { display: block; }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Error / Success messages ────────────────────── */
        .alert {
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 20px;
            display: none;
        }

        .alert.show { display: flex; align-items: flex-start; gap: 10px; }

        .alert-error {
            background: var(--error-bg);
            border: 1px solid rgba(224,82,82,0.25);
            color: #f08080;
        }

        .alert-error svg { flex-shrink: 0; margin-top: 1px; }

        /* ── Field error ─────────────────────────────────── */
        .field-error {
            font-size: 11.5px;
            color: var(--error);
            margin-top: 5px;
            display: none;
        }

        .form-input.is-invalid { border-color: rgba(224,82,82,0.5); }

        /* ── Footer ──────────────────────────────────────── */
        .login-footer {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-footer-text {
            font-size: 11px;
            color: var(--muted);
            text-align: center;
            line-height: 1.5;
        }

        .login-footer-text svg { vertical-align: -2px; margin-right: 4px; }

        /* ── Responsive ──────────────────────────────────── */
        @media (max-width: 480px) {
            .login-card { padding: 28px 24px; }
        }
    </style>
</head>
<body>

<div class="login-bg"></div>

<!-- Mountain silhouette decoration -->
<svg class="mountain-decor" viewBox="0 0 1440 220" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0 178 L180 62 L310 110 L420 58 L540 85 L650 42 L760 72 L870 30 L980 68 L1090 88 L1200 50 L1300 80 L1440 110 L1440 220 L0 220Z"
          fill="rgba(59,74,47,0.18)"/>
    <path d="M0 200 L240 95 L370 138 L500 90 L620 118 L740 70 L850 100 L980 120 L1100 78 L1220 105 L1440 140 L1440 220 L0 220Z"
          fill="rgba(59,74,47,0.12)"/>
</svg>

<div class="login-wrapper">
    <div class="login-card">

        <!-- Logo -->
        <div class="login-logo">
            <svg width="38" height="28" viewBox="0 0 300 220" xmlns="http://www.w3.org/2000/svg">
                <line x1="150" y1="29" x2="150" y2="9"  stroke="#C49018" stroke-width="10" stroke-linecap="round"/>
                <line x1="128" y1="34" x2="120" y2="14" stroke="#C49018" stroke-width="9"  stroke-linecap="round"/>
                <line x1="172" y1="34" x2="180" y2="14" stroke="#C49018" stroke-width="9"  stroke-linecap="round"/>
                <line x1="109" y1="47" x2="93"  y2="31" stroke="#C49018" stroke-width="8"  stroke-linecap="round"/>
                <line x1="191" y1="47" x2="207" y2="31" stroke="#C49018" stroke-width="8"  stroke-linecap="round"/>
                <line x1="96"  y1="67" x2="76"  y2="58" stroke="#C49018" stroke-width="7"  stroke-linecap="round"/>
                <line x1="204" y1="67" x2="224" y2="58" stroke="#C49018" stroke-width="7"  stroke-linecap="round"/>
                <path d="M 98 88 A 52 52 0 0 1 202 88" fill="none" stroke="#C49018" stroke-width="13" stroke-linecap="round"/>
                <path d="M 0 178 L 85 58 L 148 96 L 190 70 L 216 85 L 242 74 L 282 130 L 300 178 L 300 220 L 0 220 Z" fill="#3B4A2F"/>
                <path d="M 85 58 L 115 92 L 148 96 L 128 104 L 96 80 Z" fill="#9B7840" opacity="0.7"/>
            </svg>
            <div class="login-logo-text">
                <span class="login-logo-name">Chapada Diamantina</span>
                <span class="login-logo-tagline">Sistema de Gestão</span>
            </div>
        </div>

        <!-- Heading -->
        <div class="login-heading">
            <h1>Acesso Restrito</h1>
        </div>
        <p class="login-subtext">Faça login com suas credenciais para continuar.</p>

        <!-- Access notice -->
        <div class="auth-notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span>Acesso permitido apenas para usuários autorizados.</span>
        </div>

        <!-- Error alert -->
        <div class="alert alert-error" id="alertError" role="alert">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <span id="alertErrorText"></span>
        </div>

        <!-- Login Form -->
        <form id="loginForm" novalidate autocomplete="on">

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="input-wrapper">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="seu@email.com"
                        autocomplete="email"
                        required
                    >
                </div>
                <div class="field-error" id="emailError"></div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Senha</label>
                <div class="input-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input has-icon-right"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="input-icon-right" id="togglePassword" aria-label="Mostrar/ocultar senha" tabindex="-1">
                        <svg id="eyeOpen" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <svg id="eyeClosed" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="display:none">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
                <div class="field-error" id="passwordError"></div>
            </div>

            <label class="checkbox-row">
                <input type="checkbox" id="remember" name="remember">
                <span class="checkbox-label">Lembrar sessão por 30 dias</span>
            </label>

            <button type="submit" class="btn-login" id="loginBtn">
                <span class="btn-text">Entrar</span>
                <div class="spinner"></div>
            </button>

        </form>

        <div class="login-footer">
            <p class="login-footer-text">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Conexão segura · Para obter acesso, contate o administrador do sistema
            </p>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    // Detect base URL dynamically
    const scriptBase = (() => {
        const s = document.currentScript || document.querySelector('script[src]');
        const path = window.location.pathname;
        // Strip the filename, get directory
        return path.replace(/\/[^/]*$/, '');
    })();

    const BASE = scriptBase;
    const API  = BASE + '/api/v1';

    const form        = document.getElementById('loginForm');
    const emailInput  = document.getElementById('email');
    const passInput   = document.getElementById('password');
    const rememberChk = document.getElementById('remember');
    const loginBtn    = document.getElementById('loginBtn');
    const alertError  = document.getElementById('alertError');
    const alertText   = document.getElementById('alertErrorText');
    const togglePass  = document.getElementById('togglePassword');
    const eyeOpen     = document.getElementById('eyeOpen');
    const eyeClosed   = document.getElementById('eyeClosed');

    // Show / hide password
    togglePass.addEventListener('click', () => {
        const isPass = passInput.type === 'password';
        passInput.type = isPass ? 'text' : 'password';
        eyeOpen.style.display   = isPass ? 'none'  : '';
        eyeClosed.style.display = isPass ? ''      : 'none';
    });

    function setLoading(on) {
        loginBtn.disabled = on;
        loginBtn.classList.toggle('loading', on);
    }

    function showError(msg) {
        alertText.textContent = msg;
        alertError.classList.add('show');
    }

    function clearErrors() {
        alertError.classList.remove('show');
        ['email', 'password'].forEach(id => {
            const input = document.getElementById(id);
            const err   = document.getElementById(id + 'Error');
            input.classList.remove('is-invalid');
            err.style.display = 'none';
            err.textContent   = '';
        });
    }

    function showFieldError(field, msg) {
        const input = document.getElementById(field);
        const err   = document.getElementById(field + 'Error');
        input.classList.add('is-invalid');
        err.textContent   = msg;
        err.style.display = 'block';
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();

        const email    = emailInput.value.trim();
        const password = passInput.value;
        const remember = rememberChk.checked;

        // Client-side validation
        let valid = true;
        if (!email) {
            showFieldError('email', 'Informe seu email.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('email', 'Email inválido.');
            valid = false;
        }
        if (!password) {
            showFieldError('password', 'Informe sua senha.');
            valid = false;
        }
        if (!valid) return;

        setLoading(true);

        try {
            // Step 1: Authenticate against Laravel API
            const res = await fetch(API + '/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ email, password, remember }),
            });

            const data = await res.json();

            if (!res.ok) {
                if (res.status === 422 && data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    const firstMsg = data.errors[firstKey][0];
                    if (firstKey === 'email' || firstKey === 'password') {
                        showFieldError(firstKey, firstMsg);
                    } else {
                        showError(firstMsg);
                    }
                } else if (res.status === 403) {
                    showError(data.message || 'Conta desativada.');
                } else if (res.status === 429) {
                    showError('Muitas tentativas. Aguarde alguns minutos e tente novamente.');
                } else {
                    showError(data.message || 'Erro ao autenticar. Tente novamente.');
                }
                return;
            }

            // Step 2: Store token in PHP session (server-side)
            const sessionRes = await fetch(BASE + '/session_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token: data.token }),
                credentials: 'same-origin',
            });

            if (!sessionRes.ok) {
                showError('Erro ao iniciar sessão. Tente novamente.');
                return;
            }

            // Step 3: Redirect to dashboard
            window.location.href = BASE + '/dashboard.php';

        } catch (err) {
            showError('Falha na conexão. Verifique sua rede e tente novamente.');
        } finally {
            setLoading(false);
        }
    });

    // Clear errors on input
    [emailInput, passInput].forEach(inp => {
        inp.addEventListener('input', () => {
            inp.classList.remove('is-invalid');
            const err = document.getElementById(inp.id + 'Error');
            if (err) { err.style.display = 'none'; err.textContent = ''; }
            alertError.classList.remove('show');
        });
    });

}());
</script>

</body>
</html>

'use client'

import { Suspense } from 'react'
import { useState, useTransition } from 'react'
import { useSearchParams } from 'next/navigation'
import { signIn } from './actions'

function LoginForm() {
  const searchParams = useSearchParams()
  const errorMsg = searchParams.get('error')
  const [showPassword, setShowPassword] = useState(false)
  const [isPending, startTransition] = useTransition()

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault()
    const formData = new FormData(e.currentTarget)
    startTransition(() => {
      signIn(formData)
    })
  }

  return (
    <>
      <style>{`
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
          --gold:         #C49018;
          --gold-dim:     rgba(196,144,24,0.18);
          --gold-glow:    rgba(196,144,24,0.08);
          --moss:         #3B4A2F;
          --bg:           #0b0e09;
          --card:         #161c10;
          --border:       rgba(255,255,255,0.07);
          --border-focus: rgba(196,144,24,0.55);
          --text:         #e8e0d0;
          --muted:        #6b7562;
          --error:        #e05252;
          --error-bg:     rgba(224,82,82,0.08);
          --font-sans:    'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
          --font-serif:   'Playfair Display', Georgia, serif;
        }

        html, body { height: 100%; font-family: var(--font-sans); background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; }

        .l-bg {
          position: fixed; inset: 0;
          background:
            radial-gradient(ellipse 80% 60% at 50% -10%, rgba(59,74,47,0.35) 0%, transparent 70%),
            radial-gradient(ellipse 50% 40% at 80% 100%, rgba(196,144,24,0.06) 0%, transparent 60%),
            linear-gradient(180deg,#0b0e09 0%,#0f1509 50%,#0a0d07 100%);
          z-index: 0;
        }

        .l-mountain { position: fixed; bottom: 0; left: 0; right: 0; height: 220px; z-index: 1; pointer-events: none; }

        .l-wrap { position: relative; z-index: 10; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }

        .l-card {
          width: 100%; max-width: 420px;
          background: var(--card);
          border: 1px solid var(--border);
          border-radius: 16px;
          padding: 40px;
          box-shadow: 0 0 0 1px rgba(255,255,255,0.03), 0 24px 64px rgba(0,0,0,0.5), 0 4px 16px rgba(0,0,0,0.3);
          backdrop-filter: blur(8px);
        }

        .l-logo { display: flex; align-items: center; gap: 12px; margin-bottom: 32px; }
        .l-logo-name { font-family: var(--font-serif); font-size: 15px; font-weight: 500; color: var(--text); letter-spacing: 0.01em; line-height: 1.2; }
        .l-logo-tag  { font-size: 10px; font-weight: 400; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; margin-top: 2px; }

        .l-h1 { font-family: var(--font-serif); font-size: 22px; font-weight: 400; color: var(--text); letter-spacing: -0.01em; margin-bottom: 6px; }
        .l-sub { font-size: 13px; color: var(--muted); margin-bottom: 28px; line-height: 1.5; }

        .l-notice {
          display: flex; align-items: center; gap: 8px;
          background: var(--gold-glow);
          border: 1px solid rgba(196,144,24,0.2);
          border-radius: 8px;
          padding: 10px 14px;
          margin-bottom: 24px;
        }
        .l-notice svg { flex-shrink: 0; color: var(--gold); }
        .l-notice span { font-size: 12px; color: rgba(196,144,24,0.9); line-height: 1.4; }

        .l-alert {
          display: flex; align-items: flex-start; gap: 10px;
          background: var(--error-bg);
          border: 1px solid rgba(224,82,82,0.25);
          border-radius: 8px;
          padding: 12px 14px;
          font-size: 13px;
          color: #f08080;
          line-height: 1.5;
          margin-bottom: 20px;
        }
        .l-alert svg { flex-shrink: 0; margin-top: 1px; }

        .l-group { margin-bottom: 16px; }

        .l-label {
          display: block; font-size: 12px; font-weight: 500;
          color: rgba(232,224,208,0.6); letter-spacing: 0.05em;
          text-transform: uppercase; margin-bottom: 7px;
        }

        .l-input-wrap { position: relative; }

        .l-input {
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
        .l-input::placeholder { color: var(--muted); }
        .l-input:focus {
          border-color: var(--border-focus);
          background: rgba(196,144,24,0.04);
          box-shadow: 0 0 0 3px rgba(196,144,24,0.08);
        }
        .l-input:-webkit-autofill,
        .l-input:-webkit-autofill:hover,
        .l-input:-webkit-autofill:focus {
          -webkit-box-shadow: 0 0 0 30px #161c10 inset;
          -webkit-text-fill-color: #e8e0d0;
          transition: background-color 5000s;
        }
        .l-input.with-icon { padding-right: 42px; }

        .l-eye {
          position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
          background: none; border: none; cursor: pointer;
          color: var(--muted); padding: 4px;
          display: flex; align-items: center;
          transition: color .15s;
        }
        .l-eye:hover { color: var(--text); }

        .l-check-row {
          display: flex; align-items: center; gap: 9px;
          margin-bottom: 24px; cursor: pointer; user-select: none;
        }
        .l-check-row input[type="checkbox"] {
          appearance: none; -webkit-appearance: none;
          width: 16px; height: 16px;
          border: 1px solid rgba(255,255,255,0.15);
          border-radius: 4px;
          background: rgba(255,255,255,0.03);
          cursor: pointer; flex-shrink: 0; position: relative;
          transition: border-color .15s, background .15s;
        }
        .l-check-row input[type="checkbox"]:checked {
          background: var(--gold); border-color: var(--gold);
        }
        .l-check-row input[type="checkbox"]:checked::after {
          content: '';
          position: absolute; inset: 0;
          background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath d='M2 6l3 3 5-5' stroke='%23000' stroke-width='1.5' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") center/10px no-repeat;
        }
        .l-check-label { font-size: 13px; color: rgba(232,224,208,0.65); }

        .l-btn {
          width: 100%;
          background: var(--gold);
          color: #0b0e09;
          border: none; border-radius: 8px;
          padding: 12px;
          font-size: 14px; font-weight: 600;
          font-family: var(--font-sans);
          cursor: pointer; letter-spacing: 0.02em;
          transition: opacity .2s, transform .1s;
          display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .l-btn:hover:not(:disabled) { opacity: .9; }
        .l-btn:active:not(:disabled) { transform: scale(0.99); }
        .l-btn:disabled { opacity: .55; cursor: not-allowed; }

        .l-spinner {
          width: 16px; height: 16px;
          border: 2px solid rgba(0,0,0,0.2);
          border-top-color: #0b0e09;
          border-radius: 50%;
          animation: l-spin .7s linear infinite;
        }
        @keyframes l-spin { to { transform: rotate(360deg); } }

        .l-footer { margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: center; }
        .l-footer-text { font-size: 11px; color: var(--muted); text-align: center; line-height: 1.5; display: flex; align-items: center; gap: 5px; }

        @media (max-width: 480px) { .l-card { padding: 28px 24px; } }
      `}</style>

      <div className="l-bg" />

      {/* Mountain silhouette */}
      <svg className="l-mountain" viewBox="0 0 1440 220" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 178 L180 62 L310 110 L420 58 L540 85 L650 42 L760 72 L870 30 L980 68 L1090 88 L1200 50 L1300 80 L1440 110 L1440 220 L0 220Z"
              fill="rgba(59,74,47,0.18)" />
        <path d="M0 200 L240 95 L370 138 L500 90 L620 118 L740 70 L850 100 L980 120 L1100 78 L1220 105 L1440 140 L1440 220 L0 220Z"
              fill="rgba(59,74,47,0.12)" />
      </svg>

      <div className="l-wrap">
        <div className="l-card">

          {/* Logo */}
          <div className="l-logo">
            <svg width="38" height="28" viewBox="0 0 300 220" xmlns="http://www.w3.org/2000/svg">
              <line x1="150" y1="29" x2="150" y2="9"  stroke="#C49018" strokeWidth="10" strokeLinecap="round"/>
              <line x1="128" y1="34" x2="120" y2="14" stroke="#C49018" strokeWidth="9"  strokeLinecap="round"/>
              <line x1="172" y1="34" x2="180" y2="14" stroke="#C49018" strokeWidth="9"  strokeLinecap="round"/>
              <line x1="109" y1="47" x2="93"  y2="31" stroke="#C49018" strokeWidth="8"  strokeLinecap="round"/>
              <line x1="191" y1="47" x2="207" y2="31" stroke="#C49018" strokeWidth="8"  strokeLinecap="round"/>
              <line x1="96"  y1="67" x2="76"  y2="58" stroke="#C49018" strokeWidth="7"  strokeLinecap="round"/>
              <line x1="204" y1="67" x2="224" y2="58" stroke="#C49018" strokeWidth="7"  strokeLinecap="round"/>
              <path d="M 98 88 A 52 52 0 0 1 202 88" fill="none" stroke="#C49018" strokeWidth="13" strokeLinecap="round"/>
              <path d="M 0 178 L 85 58 L 148 96 L 190 70 L 216 85 L 242 74 L 282 130 L 300 178 L 300 220 L 0 220 Z" fill="#3B4A2F"/>
              <path d="M 85 58 L 115 92 L 148 96 L 128 104 L 96 80 Z" fill="#9B7840" opacity="0.7"/>
            </svg>
            <div style={{ display: 'flex', flexDirection: 'column' }}>
              <span className="l-logo-name">Chapada Diamantina</span>
              <span className="l-logo-tag">Sistema de Gestão</span>
            </div>
          </div>

          {/* Heading */}
          <h1 className="l-h1">Acesso Restrito</h1>
          <p className="l-sub">Faça login com suas credenciais para continuar.</p>

          {/* Shield notice */}
          <div className="l-notice">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span>Acesso permitido apenas para usuários autorizados.</span>
          </div>

          {/* Error */}
          {errorMsg && (
            <div className="l-alert" role="alert">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
              </svg>
              <span>{errorMsg}</span>
            </div>
          )}

          {/* Form */}
          <form onSubmit={handleSubmit} noValidate autoComplete="on">

            <div className="l-group">
              <label htmlFor="email" className="l-label">Email</label>
              <div className="l-input-wrap">
                <input
                  id="email"
                  name="email"
                  type="email"
                  required
                  autoComplete="email"
                  className="l-input"
                  placeholder="seu@email.com"
                />
              </div>
            </div>

            <div className="l-group">
              <label htmlFor="password" className="l-label">Senha</label>
              <div className="l-input-wrap">
                <input
                  id="password"
                  name="password"
                  type={showPassword ? 'text' : 'password'}
                  required
                  autoComplete="current-password"
                  className="l-input with-icon"
                  placeholder="••••••••"
                />
                <button
                  type="button"
                  className="l-eye"
                  aria-label="Mostrar/ocultar senha"
                  tabIndex={-1}
                  onClick={() => setShowPassword(v => !v)}
                >
                  {showPassword ? (
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                      <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                      <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                      <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                  ) : (
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  )}
                </button>
              </div>
            </div>

            <label className="l-check-row">
              <input type="checkbox" name="remember" />
              <span className="l-check-label">Lembrar sessão por 30 dias</span>
            </label>

            <button type="submit" className="l-btn" disabled={isPending}>
              {isPending ? <span className="l-spinner" /> : 'Entrar'}
            </button>
          </form>

          <div className="l-footer">
            <p className="l-footer-text">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              Conexão segura · Para obter acesso, contate o administrador
            </p>
          </div>

        </div>
      </div>
    </>
  )
}

export default function LoginPage() {
  return (
    <Suspense>
      <LoginForm />
    </Suspense>
  )
}


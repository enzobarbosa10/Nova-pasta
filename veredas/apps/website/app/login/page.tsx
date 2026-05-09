import { signIn } from './actions'

export default function LoginPage() {
  return (
    <main style={{ maxWidth: 400, margin: '80px auto', padding: '0 16px' }}>
      <h1>Entrar</h1>
      <form action={signIn}>
        <div style={{ marginBottom: 12 }}>
          <label htmlFor="email">E-mail</label>
          <br />
          <input
            id="email"
            name="email"
            type="email"
            required
            autoComplete="email"
            style={{ width: '100%', padding: 8, marginTop: 4 }}
          />
        </div>
        <div style={{ marginBottom: 16 }}>
          <label htmlFor="password">Senha</label>
          <br />
          <input
            id="password"
            name="password"
            type="password"
            required
            autoComplete="current-password"
            style={{ width: '100%', padding: 8, marginTop: 4 }}
          />
        </div>
        <button type="submit" style={{ width: '100%', padding: 10 }}>
          Entrar
        </button>
      </form>
    </main>
  )
}

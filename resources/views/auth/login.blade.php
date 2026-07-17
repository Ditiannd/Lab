<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — LabTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-font-smoothing: antialiased; box-sizing: border-box; }
        body { background: linear-gradient(135deg, #3a2a94 0%, #16213e 50%, #0f3460 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', sans-serif; margin: 0; padding: 20px; }
        .login-card { background: rgba(255,255,255,0.07); backdrop-filter: blur(40px); -webkit-backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.15); border-radius: 28px; padding: 40px 36px; width: 100%; max-width: 400px; box-shadow: 0 30px 80px rgba(0,0,0,0.4); }
        .logo-box { width: 70px; height: 70px; background: linear-gradient(135deg, #007AFF, #5856D6); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 8px 20px rgba(0,122,255,0.4); }
        h1 { font-size: 26px; font-weight: 700; color: white; text-align: center; margin: 0 0 4px; letter-spacing: -0.5px; }
        p.subtitle { font-size: 14px; color: rgba(255,255,255,0.5); text-align: center; margin: 0 0 28px; }
        label { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.7); display: block; margin-bottom: 6px; }
        input { width: 100%; background: rgba(255,255,255,0.1); border: 1.5px solid rgba(255,255,255,0.15); border-radius: 14px; padding: 13px 16px; font-size: 15px; color: white; outline: none; transition: all 0.2s; }
        input::placeholder { color: rgba(255,255,255,0.35); }
        input:focus { background: rgba(255,255,255,0.15); border-color: #007AFF; box-shadow: 0 0 0 3px rgba(0,122,255,0.25); }
        .form-group { margin-bottom: 16px; }
        .btn-login { width: 100%; background: linear-gradient(135deg, #007AFF, #5856D6); border: none; border-radius: 14px; padding: 15px; font-size: 16px; font-weight: 700; color: white; cursor: pointer; transition: all 0.2s; margin-top: 8px; letter-spacing: 0.2px; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,122,255,0.4); }
        .btn-login:active { transform: scale(0.98); }
        .error-msg { background: rgba(255,59,48,0.15); border: 1px solid rgba(255,59,48,0.3); color: #FF6B6B; border-radius: 12px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; }
        .divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.1); }
        .divider span { font-size: 12px; color: rgba(255,255,255,0.3); white-space: nowrap; }
        .role-badges { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
        .role-badge { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 6px 12px; font-size: 12px; color: rgba(255,255,255,0.5); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-box">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
                <path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
            </svg>
        </div>
        <h1>LabTrack</h1>
        <p class="subtitle">Sistem Peminjaman Alat Praktikum</p>

        @if(session('status'))
            <div class="error-msg" style="background:rgba(52,199,89,0.15); border-color:rgba(52,199,89,0.3); color:#34C759;">{{ session('status') }}</div>
        @endif

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                @error('email')<div style="color:#FF6B6B; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="••••••••" required>
                @error('password')<div style="color:#FF6B6B; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="divider"><span>Hak Akses Tersedia</span></div>
        <div class="role-badges">
            <span class="role-badge">👨‍💼 Admin</span>
            <span class="role-badge">🔧 Petugas Lab</span>
            <span class="role-badge">👨‍🎓 Siswa</span>
        </div>
    </div>
</body>
</html>

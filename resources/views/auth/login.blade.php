<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Bengkel Jaya Motor</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0f172a;
            --bg-card: rgba(30, 41, 59, 0.75);
            --bg-card-hover: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-primary: #3b82f6;
            --accent-purple: #8b5cf6;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --border-color: #334155;
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.15) 0px, transparent 50%);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
        }

        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-logo {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.35);
            margin-bottom: 14px;
        }

        .brand-header h1 {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-header p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444;
            color: #f87171;
        }

        .alert-info {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid #3b82f6;
            color: #60a5fa;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 15px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
            background: rgba(15, 23, 42, 0.9);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
        }

        /* Demo Quick-Login Buttons */
        .demo-section {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px dashed var(--border-color);
        }

        .demo-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 12px;
            text-align: center;
        }

        .demo-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .demo-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            color: var(--text-main);
        }

        .demo-btn:hover {
            background: rgba(51, 65, 85, 0.8);
            border-color: #60a5fa;
            transform: translateX(4px);
        }

        .demo-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .demo-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .demo-avatar.owner { background: linear-gradient(135deg, #8b5cf6, #6366f1); }
        .demo-avatar.cashier { background: linear-gradient(135deg, #10b981, #059669); }
        .demo-avatar.mechanic { background: linear-gradient(135deg, #f59e0b, #d97706); }

        .demo-name {
            font-size: 13px;
            font-weight: 600;
        }

        .demo-email {
            font-size: 11px;
            color: var(--text-muted);
        }

        .demo-badge {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-owner { background: rgba(139, 92, 246, 0.2); color: #c084fc; border: 1px solid rgba(139, 92, 246, 0.4); }
        .badge-cashier { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }
        .badge-mechanic { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="brand-header">
            <div class="brand-logo"><i class="fa-solid fa-wrench"></i></div>
            <h1>JAYA MOTOR</h1>
            <p>Sistem Manajemen Operasional & Hak Akses Bengkel</p>
        </div>

        <div class="card">
            <div class="card-title">
                <i class="fa-solid fa-right-to-bracket" style="color: var(--accent-primary);"></i>
                <span>Masuk ke Akun Anda</span>
            </div>

            @if($errors->has('email'))
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" id="email" class="form-control" placeholder="nama@jayamotor.id" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember" id="remember" style="accent-color: var(--accent-primary);">
                        <span>Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Masuk System</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <!-- Quick Demo Role Login Buttons -->
            <div class="demo-section">
                <div class="demo-title">Quick Demo Login (Klik untuk Masuk)</div>
                <div class="demo-grid">
                    <button type="button" class="demo-btn" onclick="quickLogin('hendra@jayamotor.id', 'password')">
                        <div class="demo-info">
                            <div class="demo-avatar owner">H</div>
                            <div>
                                <div class="demo-name">Pak Hendra</div>
                                <div class="demo-email">hendra@jayamotor.id</div>
                            </div>
                        </div>
                        <span class="demo-badge badge-owner">Owner</span>
                    </button>

                    <button type="button" class="demo-btn" onclick="quickLogin('rina@jayamotor.id', 'password')">
                        <div class="demo-info">
                            <div class="demo-avatar cashier">R</div>
                            <div>
                                <div class="demo-name">Mbak Rina</div>
                                <div class="demo-email">rina@jayamotor.id</div>
                            </div>
                        </div>
                        <span class="demo-badge badge-cashier">Kasir</span>
                    </button>

                    <button type="button" class="demo-btn" onclick="quickLogin('sarno@jayamotor.id', 'password')">
                        <div class="demo-info">
                            <div class="demo-avatar mechanic">S</div>
                            <div>
                                <div class="demo-name">Pak Sarno (Senior)</div>
                                <div class="demo-email">sarno@jayamotor.id</div>
                            </div>
                        </div>
                        <span class="demo-badge badge-mechanic">Mekanik</span>
                    </button>

                    <button type="button" class="demo-btn" onclick="quickLogin('junior@jayamotor.id', 'password')">
                        <div class="demo-info">
                            <div class="demo-avatar mechanic" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">J</div>
                            <div>
                                <div class="demo-name">Junior Mechanic</div>
                                <div class="demo-email">junior@jayamotor.id</div>
                            </div>
                        </div>
                        <span class="demo-badge badge-mechanic" style="background: rgba(14, 165, 233, 0.2); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.4);">Mekanik Jr</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        function quickLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('loginForm').submit();
        }
    </script>
</body>
</html>

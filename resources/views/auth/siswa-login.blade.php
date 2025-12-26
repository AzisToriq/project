<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAKAD PRO - Login Siswa</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700|inter:400,500,600" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- MODIFIKASI WARNA KHUSUS SISWA --- */
        :root {
            --primary-blue: #4f46e5; /* Indigo */
            --primary-blue-dark: #4338ca;
            --primary-green: #06b6d4; /* Cyan */
            --primary-green-dark: #0891b2;
            --gradient-start: #4f46e5;
            --gradient-end: #06b6d4;

            /* Sisa CSS Variable sama */
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --bg-light: #f8fafc;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.12);
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        /* --- SISA CSS SAMA PERSIS DENGAN PUNYA LU --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: var(--bg-light); position: relative; overflow-x: hidden; }
        .background-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .gradient-bg { position: absolute; width: 100%; height: 100%; background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%); opacity: 0.9; }
        .floating-elements { position: absolute; width: 100%; height: 100%; top: 0; left: 0; }
        .floating-element { position: absolute; opacity: 0.1; color: white; font-size: 2rem; animation: float 20s infinite linear; }
        .floating-element:nth-child(1) { top: 10%; left: 5%; animation-delay: 0s; }
        .floating-element:nth-child(2) { top: 20%; right: 10%; animation-delay: 4s; }
        .floating-element:nth-child(3) { bottom: 30%; left: 15%; animation-delay: 8s; }
        .floating-element:nth-child(4) { bottom: 15%; right: 20%; animation-delay: 12s; }
        .floating-element:nth-child(5) { top: 40%; left: 25%; animation-delay: 16s; }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-30px) rotate(10deg); } }
        .main-container { display: flex; width: 90%; max-width: 1100px; min-height: 650px; border-radius: 24px; overflow: hidden; box-shadow: var(--card-shadow); background-color: white; position: relative; z-index: 1; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .main-container:hover { box-shadow: var(--card-shadow-hover); transform: translateY(-5px); }
        .info-panel { flex: 1; background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-green) 100%); padding: 50px 40px; color: white; display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; }
        .info-panel::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); opacity: 0.3; }
        .logo-container { display: flex; align-items: center; margin-bottom: 30px; }
        .logo-icon { width: 50px; height: 50px; background-color: rgba(255, 255, 255, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 24px; }
        .logo-text h1 { font-size: 28px; font-weight: 700; line-height: 1.2; }
        .logo-text p { font-size: 14px; opacity: 0.9; font-weight: 300; }
        .info-content { flex-grow: 1; display: flex; flex-direction: column; justify-content: center; }
        .info-title { font-size: 36px; font-weight: 700; margin-bottom: 20px; line-height: 1.2; }
        .info-description { font-size: 16px; line-height: 1.6; margin-bottom: 30px; opacity: 0.9; max-width: 90%; }
        .features { margin-top: 30px; }
        .feature { display: flex; align-items: center; margin-bottom: 15px; }
        .feature-icon { width: 24px; height: 24px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 12px; }
        .feature-text { font-size: 14px; }
        .login-panel { flex: 1; padding: 50px 40px; display: flex; flex-direction: column; justify-content: center; background-color: white; }
        .login-header { text-align: center; margin-bottom: 40px; }
        .login-title { font-size: 32px; color: var(--text-dark); margin-bottom: 10px; font-weight: 700; }
        .login-subtitle { color: var(--text-light); font-size: 16px; }
        .form-container { width: 100%; }
        .input-group { margin-bottom: 25px; position: relative; }
        .input-label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-dark); font-size: 14px; }
        .input-container { position: relative; }
        .input-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-light); font-size: 18px; }
        .custom-input { width: 100%; padding: 15px 15px 15px 50px; border: 2px solid #e9ecef; border-radius: 12px; font-size: 15px; transition: all 0.3s ease; background-color: #fafbfc; }
        .custom-input:focus { outline: none; border-color: var(--primary-blue); background-color: white; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .custom-input.error { border-color: var(--error-color); }
        .password-toggle { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-light); cursor: pointer; font-size: 18px; transition: all 0.2s; }
        .password-toggle:hover { color: var(--primary-blue); }
        #toggleEmailClear { display: none; }
        #toggleEmailClear.visible { display: block; }
        .form-options { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; font-size: 14px; }
        .remember-me { display: flex; align-items: center; }
        .remember-me input { margin-right: 8px; width: 16px; height: 16px; accent-color: var(--primary-blue); }
        .forgot-password { color: var(--primary-blue); text-decoration: none; font-weight: 500; transition: color 0.2s; }
        .forgot-password:hover { color: var(--primary-blue-dark); text-decoration: underline; }
        .btn-primary { width: 100%; padding: 16px; background: linear-gradient(to right, var(--primary-blue), var(--primary-green)); color: white; border: none; border-radius: 12px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 25px; }
        .btn-primary:hover { background: linear-gradient(to right, var(--primary-blue-dark), var(--primary-green-dark)); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
        .login-footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #f1f3f5; color: var(--text-light); font-size: 12px; }
        .login-footer a { color: var(--primary-blue); text-decoration: none; }
        .error-message { color: var(--error-color); font-size: 12px; margin-top: 5px; display: flex; align-items: center; gap: 5px; }
        .auth-session-status { background-color: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #10b981; }
        .auth-session-status.error { background-color: #fee2e2; color: #991b1b; border-left-color: #ef4444; }
        @media (max-width: 900px) { .main-container { flex-direction: column; width: 95%; max-width: 500px; min-height: auto; } .info-panel { padding: 30px; } .login-panel { padding: 30px; } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .login-panel { animation: fadeInUp 0.6s ease-out; }
    </style>
</head>
<body>
    <div class="background-container">
        <div class="gradient-bg"></div>
        <div class="floating-elements">
            <div class="floating-element"><i class="fas fa-book-open"></i></div>
            <div class="floating-element"><i class="fas fa-atom"></i></div>
            <div class="floating-element"><i class="fas fa-laptop-code"></i></div>
            <div class="floating-element"><i class="fas fa-globe"></i></div>
            <div class="floating-element"><i class="fas fa-pencil-alt"></i></div>
        </div>
    </div>

    <div class="main-container">
        <div class="info-panel">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="logo-text">
                    <h1>PORTAL SISWA</h1>
                    <p>SIAKAD PRO</p>
                </div>
            </div>

            <div class="info-content">
                <h2 class="info-title">Mulai Perjalanan Belajarmu Di Sini</h2>
                <p class="info-description">
                    Akses materi pelajaran, kumpulkan tugas, cek jadwal, dan lihat hasil belajarmu dalam satu aplikasi.
                </p>

                <div class="features">
                    <div class="feature"><div class="feature-icon"><i class="fas fa-calendar-alt"></i></div><div class="feature-text">Cek Jadwal Pelajaran</div></div>
                    <div class="feature"><div class="feature-icon"><i class="fas fa-file-upload"></i></div><div class="feature-text">Kirim Tugas Online</div></div>
                    <div class="feature"><div class="feature-icon"><i class="fas fa-chart-line"></i></div><div class="feature-text">Pantau Nilai & Rapor</div></div>
                </div>
            </div>

            <div class="info-footer">
                <p>© 2025 SIAKAD PRO. Student Portal.</p>
            </div>
        </div>

        <div class="login-panel">
            <div class="login-header">
                <h2 class="login-title">Login Siswa</h2>
                <p class="login-subtitle">Masukkan akun siswa yang terdaftar</p>
            </div>

            @if (session('status'))
                <div class="auth-session-status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="auth-session-status error"><i class="fas fa-exclamation-circle"></i> Kredensial Salah.</div>
            @endif

            <div class="form-container">
                <form method="POST" action="{{ route('siswa.authenticate') }}" id="loginForm">
                    @csrf

                    <div class="input-group">
                        <label for="username" class="input-label">Username / NIS</label>
                        <div class="input-container">
                            <div class="input-icon"><i class="fas fa-id-card"></i></div>
                            <input id="username" type="text" name="username" value="{{ old('username') }}"
                                   class="custom-input @error('username') error @enderror"
                                   placeholder="Contoh: 250001" required autofocus>
                            <div class="password-toggle" id="toggleEmailClear"><i class="fas fa-times"></i></div>
                        </div>
                        @error('username') <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    </div>

                    <div class="input-group">
                        <label for="password" class="input-label">Kata Sandi</label>
                        <div class="input-container">
                            <div class="input-icon"><i class="fas fa-lock"></i></div>
                            <input id="password" type="password" name="password" class="custom-input @error('password') error @enderror" placeholder="••••••••" required>
                            <div class="password-toggle" id="togglePassword"><i class="fas fa-eye"></i></div>
                        </div>
                        @error('password') <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    </div>

                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember_me" name="remember">
                            <label for="remember_me">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>MASUK KELAS</span>
                    </button>
                </form>
            </div>

            <div class="login-footer">
                <p>Orang Tua? <a href="{{ route('ortu.login') }}">Login Wali Murid</a></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('username'); // Ganti ID jadi username
            const clearEmailBtn = document.getElementById('toggleEmailClear');
            const passwordInput = document.getElementById('password');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('span');
            const btnIcon = submitBtn.querySelector('i');

            function updateClearBtnVisibility() {
                if (emailInput.value.length > 0) clearEmailBtn.classList.add('visible');
                else clearEmailBtn.classList.remove('visible');
            }
            updateClearBtnVisibility();
            emailInput.addEventListener('input', updateClearBtnVisibility);
            clearEmailBtn.addEventListener('click', function() {
                emailInput.value = ''; emailInput.focus(); updateClearBtnVisibility();
            });
            togglePasswordBtn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') { passwordInput.type = 'text'; icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
                else { passwordInput.type = 'password'; icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
            });
            document.getElementById('loginForm').addEventListener('submit', function() {
                if(this.checkValidity()){
                    btnText.innerText = 'MEMUAT...'; btnIcon.className = 'fas fa-spinner fa-spin';
                    submitBtn.style.opacity = '0.8'; submitBtn.style.pointerEvents = 'none';
                }
            });
            document.querySelectorAll('.custom-input').forEach(input => {
                input.addEventListener('focus', function() { this.parentElement.style.transform = 'scale(1.02)'; });
                input.addEventListener('blur', function() { this.parentElement.style.transform = 'scale(1)'; });
            });
        });
    </script>
</body>
</html>

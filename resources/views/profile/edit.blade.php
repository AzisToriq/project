@extends('layouts.app')

@section('content')
<div class="py-4">

    <div class="d-flex align-items-center mb-4">
        <h1 class="h3 text-gray-800 font-weight-bold">👤 Pengaturan Akun</h1>
    </div>

    <div class="row">

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control bg-light border-0" value="{{ old('name', $user->name) }}" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Email (Login)</label>
                            <input type="email" name="email" class="form-control bg-light border-0" value="{{ old('email', $user->email) }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="fas fa-save me-2"></i> Simpan Profil
                            </button>
                        </div>

                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success small border-0 bg-success bg-opacity-10 text-success mt-3 mb-0">
                                <i class="fas fa-check-circle me-1"></i> Profil berhasil diperbarui.
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-warning">Ganti Password</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control bg-light border-0">
                            @error('current_password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Password Baru</label>
                            <input type="password" name="password" class="form-control bg-light border-0">
                            @error('password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control bg-light border-0">
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-warning text-white rounded-pill px-4">
                                <i class="fas fa-key me-2"></i> Update Password
                            </button>
                        </div>

                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success small border-0 bg-success bg-opacity-10 text-success mt-3 mb-0">
                                <i class="fas fa-check-circle me-1"></i> Password berhasil diubah.
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg border-start border-4 border-danger">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-danger mb-1">Hapus Akun</h6>
                        <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan. Semua data Anda akan hilang.</p>
                    </div>
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-4" onclick="confirmDeleteAccount()">
                        Hapus Permanen
                    </button>
                </div>
            </div>
        </div>

        <form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" class="d-none">
            @csrf @method('delete')
            <input type="password" name="password" value="password" required> </form>

    </div>
</div>

<script>
    function confirmDeleteAccount() {
        if(confirm("Apakah Anda yakin ingin menghapus akun ini?")) {
            // Logic standar Laravel butuh input password,
            // disini kita bisa pakai Modal atau redirect logic lain.
            // Untuk simpelnya, kita minta user isi password manual via prompt browser
            let pass = prompt("Masukkan password Anda untuk konfirmasi:");
            if(pass) {
                let form = document.getElementById('delete-account-form');
                form.querySelector('input[name="password"]').value = pass;
                form.submit();
            }
        }
    }
</script>
@endsection

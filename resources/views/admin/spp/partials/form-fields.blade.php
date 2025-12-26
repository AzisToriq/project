{{-- Input Judul Tagihan (Baru) --}}
<div class="mb-3">
    <label class="form-label fw-bold small">Judul / Keterangan Tagihan</label>
    <input type="text" name="title" class="form-control bg-light border-0"
           placeholder="Contoh: SPP Januari 2025, Uang Buku, Denda..." required>
</div>

<div class="row g-2 mb-3">
    <div class="col-6">
        <label class="form-label fw-bold small">Bulan</label>
        <select name="month" class="form-select bg-light border-0" required>
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                <option value="{{ $bulan }}" {{ date('n') == $loop->iteration ? 'selected' : '' }}>{{ $bulan }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6">
        <label class="form-label fw-bold small">Tahun</label>
        <input type="number" name="year" class="form-control bg-light border-0" value="{{ date('Y') }}" required>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-bold small">Nominal (Rp)</label>
    <input type="number" name="amount" class="form-control bg-light border-0" placeholder="150000" required>
</div>

<div class="mb-3">
    <label class="form-label fw-bold small text-danger">Batas Pembayaran (Jatuh Tempo)</label>
    <input type="date" name="due_date" class="form-control bg-light border-0" required min="{{ date('Y-m-d') }}">
    <div class="form-text small text-muted">Tanggal ini akan muncul di notifikasi Wali Murid.</div>
</div>

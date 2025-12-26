<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor - {{ $student->nama_lengkap ?? 'Siswa' }}</title>

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* SETUP HALAMAN A4 PROFESSIONAL */
        body {
            background-color: #525659;
            font-family: 'Times New Roman', Times, serif; /* Font Resmi Rapor */
            padding: 20px 0;
            margin: 0;
        }

        .a4-paper {
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 20mm; /* Margin Standar Dokumen Resmi */
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
            position: relative;
        }

        /* Styling Tabel Rapor */
        .table-bordered { border-color: black !important; }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid black !important;
            vertical-align: middle;
            padding: 6px 8px; /* Padding pas */
        }

        .bg-header { background-color: #e9ecef !important; } /* Warna Header Tabel Abu Lembut */

        /* Kop Surat Resmi */
        .header-kop {
            border-bottom: 3px double black; /* Garis bawah ganda tebal */
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        /* Mode Cetak */
        @media print {
            body { background: white; padding: 0; margin: 0; -webkit-print-color-adjust: exact; }
            .a4-paper { width: 100%; margin: 0; box-shadow: none; padding: 0; min-height: auto; }
            .no-print { display: none !important; }
            .bg-header { background-color: #e9ecef !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    {{-- NAVIGASI (Hanya di Layar) --}}
    <div class="container py-3 no-print" style="max-width: 210mm;">
        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm border">
            {{-- Logic Tombol Kembali Pintar --}}
            @if(Auth::check() && Auth::user()->role == 'guru')
                <a href="{{ route('guru.data.students.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Data Siswa
                </a>
            @elseif(Auth::check() && Auth::user()->role == 'student')
                <a href="{{ url('/dashboard') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i> Dashboard
                </a>
            @else
                <a href="#" class="btn btn-secondary btn-sm" onclick="history.back()">
                    <i class="fas fa-arrow-left me-2"></i> Kembali
                </a>
            @endif

            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">
                <i class="fas fa-print me-2"></i> CETAK RAPOR
            </button>
        </div>
    </div>

    {{-- KERTAS A4 --}}
    <div class="a4-paper">

        {{-- KOP SURAT --}}
        <div class="text-center header-kop">
            <h4 class="fw-bold text-uppercase mb-1" style="font-size: 16pt;">Laporan Hasil Belajar</h4>
            <h3 class="fw-bold text-uppercase" style="font-size: 20pt; letter-spacing: 1px;">SD Baitul Insan</h3>
            <p class="mb-0 fst-italic">Jl. Pendidikan No. 123, Kota Bandar Lampung</p>
        </div>

        {{-- IDENTITAS SISWA --}}
        <div class="row mb-4 small" style="font-size: 11pt;">
            <div class="col-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td width="130">Nama Peserta Didik</td><td width="10">:</td><td class="fw-bold">{{ $student->nama_lengkap }}</td></tr>
                    <tr><td>NISN / NIS</td><td>:</td><td>{{ $student->nisn }} / {{ $student->nis ?? '-' }}</td></tr>
                    <tr><td>Kelas</td><td>:</td><td>{{ $student->classroom->nama_kelas ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td width="120">Fase</td><td width="10">:</td>
                        <td>
                            @php $lvl = $student->classroom->level ?? 0; @endphp
                            @if(in_array($lvl, [1, 2])) A (Kelas 1-2)
                            @elseif(in_array($lvl, [3, 4])) B (Kelas 3-4)
                            @elseif(in_array($lvl, [5, 6])) C (Kelas 5-6)
                            @else - @endif
                        </td>
                    </tr>
                    <tr><td>Semester</td><td>:</td><td>1 (Ganjil)</td></tr>
                    <tr><td>Tahun Ajaran</td><td>:</td><td>2025/2026</td></tr>
                </table>
            </div>
        </div>

        {{-- A. NILAI AKADEMIK --}}
        <div class="mb-2 fw-bold" style="font-size: 11pt;">A. NILAI AKADEMIK</div>
        <table class="table table-bordered table-sm mb-4 small" style="font-size: 10pt;">
            <thead class="bg-header text-center align-middle">
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Mata Pelajaran</th>
                    <th width="8%">KKM</th>
                    <th width="10%">Nilai Akhir</th>
                    <th width="10%">Predikat</th>
                    <th width="32%">Deskripsi Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allocations as $index => $alloc)
                    @php
                        // Logic Nilai dengan Safety Check (?? 0)
                        $harian1 = $grades[$alloc->id]['uh1'] ?? 0;
                        $harian2 = $grades[$alloc->id]['uh2'] ?? 0;
                        $uts     = $grades[$alloc->id]['uts'] ?? 0;
                        $uas     = $grades[$alloc->id]['uas'] ?? 0;

                        // Hitung
                        $rataHarian = ($harian1 + $harian2) / 2;
                        $nilaiAkhir = ($rataHarian * 0.4) + ($uts * 0.3) + ($uas * 0.3);
                        $nilaiAkhir = round($nilaiAkhir);

                        // Predikat
                        if ($nilaiAkhir >= 90) { $predikat = 'A'; $desc = 'Sangat Baik'; }
                        elseif ($nilaiAkhir >= 80) { $predikat = 'B'; $desc = 'Baik'; }
                        elseif ($nilaiAkhir >= 70) { $predikat = 'C'; $desc = 'Cukup'; }
                        elseif ($nilaiAkhir >= 60) { $predikat = 'D'; $desc = 'Kurang'; }
                        else { $predikat = 'E'; $desc = 'Perlu Bimbingan'; }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $alloc->subject->nama_mapel ?? $alloc->subject->name }}</div>
                            <div class="text-muted fst-italic" style="font-size: 8pt;">
                                Guru: {{ $alloc->teacher->nama_lengkap ?? $alloc->teacher->user->name ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">75</td>
                        <td class="text-center fw-bold">{{ $nilaiAkhir }}</td>
                        <td class="text-center">{{ $predikat }}</td>
                        <td style="font-size: 9pt;">
                            Ananda {{ explode(' ', $student->nama_lengkap)[0] }} menunjukkan penguasaan yang <b>{{ $desc }}</b> dalam pembelajaran ini.
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 fst-italic">Data nilai belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- B. EKSKUL & C. ABSENSI --}}
        <div class="row mb-4 small" style="font-size: 10pt;">

            {{-- B. EKSKUL --}}
            <div class="col-7">
                <div class="mb-2 fw-bold" style="font-size: 11pt;">B. EKSTRAKURIKULER</div>
                <table class="table table-bordered table-sm">
                    <thead class="bg-header text-center">
                        <tr><th>Kegiatan</th><th width="15%">Predikat</th><th>Keterangan</th></tr>
                    </thead>
                    <tbody>
                        {{-- ANTI CRASH: Pakai ?? [] agar loop tidak error jika variabel hilang --}}
                        @forelse($ekskul ?? [] as $eks)
                            <tr>
                                <td>{{ $eks['nama'] }}</td>
                                <td class="text-center">{{ $eks['predikat'] }}</td>
                                <td>{{ $eks['keterangan'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">-</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- C. ABSENSI --}}
            <div class="col-5">
                <div class="mb-2 fw-bold" style="font-size: 11pt;">C. KETIDAKHADIRAN</div>
                <table class="table table-bordered table-sm">
                    {{-- ANTI CRASH: Pakai ?? 0 jika index array tidak ada --}}
                    <tr><td>Sakit</td><td class="text-center" width="30%">{{ $absensi['sakit'] ?? 0 }} Hari</td></tr>
                    <tr><td>Izin</td><td class="text-center">{{ $absensi['izin'] ?? 0 }} Hari</td></tr>
                    <tr><td>Tanpa Keterangan</td><td class="text-center">{{ $absensi['alpha'] ?? 0 }} Hari</td></tr>
                </table>
            </div>
        </div>

        {{-- D. CATATAN --}}
        <div class="mb-5 small">
            <div class="mb-2 fw-bold" style="font-size: 11pt;">D. CATATAN WALI KELAS</div>
            <div class="border border-dark p-3" style="min-height: 80px;">
                <p class="mb-0 fst-italic">
                    "Selamat atas pencapaian di semester ini. Tingkatkan terus semangat belajar dan prestasi Ananda {{ explode(' ', $student->nama_lengkap)[0] }}."
                </p>
            </div>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="row small mt-5 pt-4" style="font-size: 11pt;">
            <div class="col-4 text-center">
                <p class="mb-5">Mengetahui,<br>Orang Tua/Wali</p>
                <br><br>
                <p class="fw-bold text-decoration-underline">( .................................... )</p>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <p class="mb-5">
                    Bandar Lampung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                    Wali Kelas
                </p>
                <br><br>
                <p class="fw-bold text-decoration-underline mb-0">{{ $student->classroom->waliKelas->nama_lengkap ?? '.......................' }}</p>
                <p>NIP. {{ $student->classroom->waliKelas->nip ?? '-' }}</p>
            </div>
        </div>

    </div>
</body>
</html>

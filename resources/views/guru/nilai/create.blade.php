@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <a href="{{ route('guru.nilai.index') }}" class="btn btn-link text-secondary p-0 mb-2 text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Kelas
            </a>
            <h1 class="h3 text-gray-800 font-weight-bold">Input & Edit Nilai</h1>
            <p class="mb-0 text-muted">
                <span class="badge bg-warning text-dark me-2 border">{{ $allocation->subject->nama_mapel }}</span>
                <span class="badge bg-dark">{{ $allocation->classroom->nama_kelas }}</span>
            </p>
        </div>
        <div class="col-md-4 text-end">
             <div class="alert alert-info py-1 px-3 d-inline-block small mb-0 shadow-sm border-0">
                <i class="fas fa-info-circle me-1"></i> Kosongkan kotak jika ingin menghapus nilai.
            </div>
        </div>
    </div>

    <form action="{{ route('guru.nilai.store', $allocation->id) }}" method="POST" id="formNilai">
        @csrf
        <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 table-striped">
                        <thead class="bg-light text-center text-uppercase small text-muted sticky-top" style="z-index: 5;">
                            <tr>
                                <th class="text-start px-4" style="width: 5%">No</th>
                                <th class="text-start" style="width: 30%">Identitas Siswa</th>
                                <th style="width: 15%">UH 1</th>
                                <th style="width: 15%">UH 2</th>
                                <th style="width: 15%">UTS</th>
                                <th style="width: 15%">UAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $student)
                            <tr>
                                <td class="px-4 text-start text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $student->nama_lengkap }}</div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">NISN: {{ $student->nisn }}</small>
                                </td>

                                {{--
                                    LOGIKA VALUE:
                                    Kita pakai null coalescing (??) agar jika data tidak ada, value-nya kosong string ''.
                                    Jika datanya '0', dia tetap tercetak '0'.
                                --}}

                                <td class="px-2">
                                    <input type="number" name="nilai[{{ $student->id }}][UH1]"
                                           class="form-control text-center border input-nilai fw-bold text-primary"
                                           min="0" max="100" placeholder="-" step="any"
                                           value="{{ $grades[$student->id]['UH1'] ?? '' }}"
                                           autocomplete="off">
                                </td>

                                <td class="px-2">
                                    <input type="number" name="nilai[{{ $student->id }}][UH2]"
                                           class="form-control text-center border input-nilai fw-bold text-primary"
                                           min="0" max="100" placeholder="-" step="any"
                                           value="{{ $grades[$student->id]['UH2'] ?? '' }}"
                                           autocomplete="off">
                                </td>

                                <td class="px-2">
                                    <input type="number" name="nilai[{{ $student->id }}][UTS]"
                                           class="form-control text-center border input-nilai fw-bold text-dark bg-warning bg-opacity-10"
                                           min="0" max="100" placeholder="-" step="any"
                                           style="border-color: #ffeeba;"
                                           value="{{ $grades[$student->id]['UTS'] ?? '' }}"
                                           autocomplete="off">
                                </td>

                                <td class="px-2">
                                    <input type="number" name="nilai[{{ $student->id }}][UAS]"
                                           class="form-control text-center border input-nilai fw-bold text-danger bg-danger bg-opacity-10"
                                           min="0" max="100" placeholder="-" step="any"
                                           style="border-color: #f5c6cb;"
                                           value="{{ $grades[$student->id]['UAS'] ?? '' }}"
                                           autocomplete="off">
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <p class="text-muted small mt-2">Tidak ada siswa terdaftar di kelas ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white p-3 text-end sticky-bottom shadow-lg" style="position: sticky; bottom: 0; z-index: 10; border-top: 1px solid #e3e6f0;">
                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow fw-bold">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    /* Style untuk input number tanpa panah spinner */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none; margin: 0;
    }
    .input-nilai:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-color: #86b7fe !important;
        transform: scale(1.05);
        transition: all 0.2s;
        z-index: 10;
        position: relative;
    }
</style>
@endsection

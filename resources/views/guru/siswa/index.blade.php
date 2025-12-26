@extends('layouts.app')

@section('content')
<div class="py-4">
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
            <i class="fas fa-users fa-lg"></i>
        </div>
        <div>
            <h1 class="h3 text-gray-800 font-weight-bold mb-0">Data Siswa Ajar</h1>
            <p class="text-muted small mb-0">Daftar siswa di kelas yang Anda ampu.</p>
        </div>
    </div>

    <div class="accordion shadow-sm rounded-lg overflow-hidden" id="accordionSiswa">

        @forelse($classrooms as $index => $class)
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="heading{{ $class->id }}">
                    <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} bg-white text-dark fw-bold"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $class->id }}"
                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">

                        <div class="d-flex align-items-center w-100 me-3">
                            <div class="me-3">
                                <span class="badge bg-primary rounded-pill px-3">Kelas {{ $class->name }}</span>
                            </div>
                            <div class="text-muted small fw-normal">
                                <i class="fas fa-user-graduate me-1"></i> {{ $class->students->count() }} Siswa
                            </div>
                        </div>

                    </button>
                </h2>
                <div id="collapse{{ $class->id }}"
                     class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                     data-bs-parent="#accordionSiswa">

                    <div class="accordion-body bg-light p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="bg-white text-muted small text-uppercase">
                                    <tr>
                                        <th class="px-4 py-3">No</th>
                                        <th>NISN / NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th>L/P</th>
                                        <th class="text-end px-4">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @forelse($class->students as $idx => $student)
                                    <tr>
                                        <td class="px-4 text-muted">{{ $idx + 1 }}</td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $student->nisn }}</span>
                                            <span class="text-muted small d-block">{{ $student->nis ?? '-' }}</span>
                                        </td>
                                        <td class="fw-bold text-dark">{{ $student->name }}</td>
                                        <td>
                                            <span class="badge {{ $student->gender == 'L' ? 'bg-info text-dark' : 'bg-danger bg-opacity-75' }} rounded-circle"
                                                  style="width: 25px; height: 25px; display: inline-flex; align-items: center; justify-content: center;">
                                                {{ $student->gender }}
                                            </span>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="{{ route('guru.data.students.rapor', $student->id) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                               title="Lihat Progress">
                                                <i class="fas fa-chart-line me-1"></i> Progress
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            Belum ada data siswa di kelas ini.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-5 text-center bg-white">
                <img src="https://img.freepik.com/free-vector/no-data-concept-illustration_114360-536.jpg" width="150" class="opacity-50 mb-3">
                <p class="text-muted">Anda belum memiliki jadwal mengajar di kelas manapun.</p>
            </div>
        @endforelse

    </div>
</div>
@endsection

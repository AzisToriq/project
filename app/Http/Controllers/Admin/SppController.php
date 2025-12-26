<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SppPayment;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SppController extends Controller
{
    /**
     * Tampilkan List Tagihan SPP
     */
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('level')->orderBy('nama_kelas')->get();

        $query = SppPayment::query();
        $query->select('spp_payments.*')
              ->join('students', 'students.id', '=', 'spp_payments.student_id')
              ->orderBy('students.nama_lengkap', 'asc')
              ->with(['student.classroom']);

        // --- FILTER ---
        if ($request->filled('classroom_id')) {
            $query->where('students.classroom_id', $request->classroom_id);
        }
        if ($request->filled('publish_status')) {
            $status = $request->publish_status == 'published' ? 1 : 0;
            $query->where('spp_payments.is_published', $status);
        }
        if ($request->filled('month')) {
            $query->where('spp_payments.month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('spp_payments.year', $request->year);
        }
        if ($request->filled('status')) {
            $query->where('spp_payments.status', $request->status);
        }

        $payments = $query->paginate(20)->withQueryString();

        return view('admin.spp.index', compact('payments', 'classrooms'));
    }

    /**
     * HALAMAN ARSIP (Paid Only)
     */
    public function archive(Request $request)
    {
        $classrooms = Classroom::orderBy('level')->orderBy('nama_kelas')->get();
        $query = SppPayment::with(['student.classroom'])->where('status', 'paid');

        if ($request->filled('classroom_id')) {
            $query->whereHas('student', fn($q) => $q->where('classroom_id', $request->classroom_id));
        }
        if ($request->filled('month')) $query->where('month', $request->month);
        if ($request->filled('year')) $query->where('year', $request->year);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn($q) => $q->where('nama_lengkap', 'like', "%$search%"));
        }

        $archives = $query->orderBy('paid_at', 'desc')->paginate(20);
        return view('admin.spp.archive', compact('archives', 'classrooms'));
    }

    /**
     * HALAMAN VERIFIKASI (Pending Only)
     */
    public function verification()
    {
        $pendingPayments = SppPayment::where('status', 'pending')
                            ->with(['student.classroom'])
                            ->orderBy('updated_at', 'asc')
                            ->get();

        return view('admin.spp.verification', compact('pendingPayments'));
    }

    /**
     * ==========================================
     * GENERATE TAGIHAN (MASSAL & SATUAN)
     * ==========================================
     */

    /**
     * GENERATE TAGIHAN MASSAL (Per Kelas)
     * UPDATE: Hapus validasi duplikasi agar bisa generate ulang/double di bulan sama
     */
    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'title' => 'required|string|max:255',
            'month' => 'required',
            'year' => 'required|integer|min:2024|max:2030',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
        ]);

        $students = Student::where('classroom_id', $request->classroom_id)->get();
        if ($students->isEmpty()) return back()->with('error', 'Kelas ini belum memiliki siswa.');

        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($students as $student) {

                // --- LOGIC LAMA (CEK DUPLIKASI) SUDAH DIHAPUS ---
                // Sekarang sistem akan langsung membuat tagihan baru meskipun
                // tagihan bulan/tahun tersebut sudah ada (Double Bill).

                SppPayment::create([
                    'student_id' => $student->id,
                    'amount' => $request->amount,
                    'title' => $request->title,
                    'due_date' => $request->due_date,
                    'month' => $request->month,
                    'year' => $request->year,
                    'status' => 'unpaid',
                    'is_published' => false,
                ]);
                $count++;
            }
            DB::commit();

            return back()->with('success', "Berhasil membuat {$count} draft tagihan untuk seluruh siswa.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Ambil Siswa by Kelas (Untuk Dropdown Satuan)
     */
    public function getStudentsByClass($classroom_id)
    {
        $students = Student::where('classroom_id', $classroom_id)
                            ->orderBy('nama_lengkap')
                            ->select('id', 'nama_lengkap')
                            ->get();
        return response()->json($students);
    }

    /**
     * GENERATE TAGIHAN SATUAN (Per Siswa)
     * UPDATE: Hapus validasi duplikasi agar bisa generate ulang/double di bulan sama
     */
    public function storeIndividual(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'month' => 'required',
            'year' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after:today',
        ]);

        // --- LOGIC LAMA (CEK DUPLIKASI) SUDAH DIHAPUS ---

        SppPayment::create([
            'student_id' => $request->student_id,
            'amount' => $request->amount,
            'title' => $request->title,
            'due_date' => $request->due_date,
            'month' => $request->month,
            'year' => $request->year,
            'status' => 'unpaid',
            'is_published' => false, // Default Draft
        ]);

        return back()->with('success', 'Berhasil membuat 1 draft tagihan spesifik.');
    }

    /**
     * ==========================================
     * MANAJEMEN DATA (UPDATE & HAPUS)
     * ==========================================
     */

    public function update(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|min:0']);
        SppPayment::findOrFail($id)->update(['amount' => $request->amount]);
        return back()->with('success', 'Nominal diperbarui.');
    }

    public function togglePublish($id)
    {
        $payment = SppPayment::findOrFail($id);
        $payment->update(['is_published' => !$payment->is_published]);
        return back()->with('success', 'Status publikasi diperbarui.');
    }

    public function publishAll(Request $request)
    {
        $query = SppPayment::query();
        if ($request->filled('classroom_id')) {
            $query->whereHas('student', fn($q) => $q->where('classroom_id', $request->classroom_id));
        }
        if ($request->filled('month')) $query->where('month', $request->month);
        if ($request->filled('year')) $query->where('year', $request->year);

        $count = $query->where('is_published', false)->update(['is_published' => true]);
        return back()->with('success', "{$count} tagihan diterbitkan!");
    }

    public function destroy($id)
    {
        $payment = SppPayment::findOrFail($id);
        if ($payment->proof_file && Storage::disk('public')->exists($payment->proof_file)) {
            Storage::disk('public')->delete($payment->proof_file);
        }
        $payment->delete();
        return back()->with('success', 'Tagihan dihapus.');
    }

    public function deleteAll(Request $request)
    {
        $query = SppPayment::query();
        if ($request->filled('classroom_id')) $query->whereHas('student', fn($q) => $q->where('classroom_id', $request->classroom_id));
        if ($request->filled('month')) $query->where('month', $request->month);
        if ($request->filled('year')) $query->where('year', $request->year);

        $count = $query->where('status', 'unpaid')->delete();
        return back()->with('success', "{$count} tagihan (Unpaid) dihapus.");
    }

    /**
     * ==========================================
     * LOGIC PEMBAYARAN (VERIFIKASI & MANUAL)
     * ==========================================
     */

    /**
     * VERIFIKASI ONLINE (Terima Bukti Transfer)
     */
    public function verify($id)
    {
        SppPayment::findOrFail($id)->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        return back()->with('success', 'Pembayaran diterima (LUNAS).');
    }

    /**
     * TOLAK PEMBAYARAN ONLINE
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['note' => 'required|string']);
        $payment = SppPayment::findOrFail($id);

        if ($payment->proof_file && Storage::disk('public')->exists($payment->proof_file)) {
            Storage::disk('public')->delete($payment->proof_file);
        }

        $payment->update([
            'status' => 'unpaid',
            'proof_file' => null,
            'note' => 'Ditolak: ' . $request->note
        ]);
        return back()->with('warning', 'Pembayaran ditolak.');
    }

    /**
     * PEMBAYARAN MANUAL (TUNAI VIA ADMIN)
     */
    public function payManual($id)
    {
        $payment = SppPayment::findOrFail($id);

        // Update jadi Lunas
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'proof_file' => null, // Tidak perlu bukti upload
            'note' => 'Pembayaran Tunai / Manual via Admin TU', // Catatan otomatis
            'dispensation_status' => null, // Reset status dispensasi (anggap selesai)
        ]);

        return back()->with('success', 'Pembayaran Tunai berhasil dicatat.');
    }

    /**
     * BATALKAN PEMBAYARAN (ROLLBACK ke UNPAID)
     */
    public function cancelPayment($id)
    {
        $payment = SppPayment::findOrFail($id);

        $payment->update([
            'status' => 'unpaid',
            'paid_at' => null,
            'note' => 'Pembayaran dibatalkan oleh Admin (Reversal).',
        ]);

        return back()->with('warning', 'Status pembayaran dikembalikan menjadi BELUM LUNAS.');
    }

    /**
     * ==========================================
     * FITUR DISPENSASI (APPROVAL & MANUAL)
     * ==========================================
     */

    /**
     * ADMIN: Input Manual Dispensasi (Auto Approve)
     */
    public function saveDispensation(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after:today',
            'note' => 'required|string|max:255',
        ]);

        SppPayment::findOrFail($id)->update([
            'dispensation_date' => $request->date,
            'dispensation_note' => $request->note . ' (Manual Admin)',
            'dispensation_status' => 'approved', // Langsung Approved
        ]);

        return back()->with('success', 'Dispensasi berhasil dicatat dan disetujui.');
    }

    /**
     * ADMIN: Setujui Pengajuan Ortu
     */
    public function approveDispensation($id)
    {
        SppPayment::findOrFail($id)->update([
            'dispensation_status' => 'approved'
        ]);
        return back()->with('success', 'Pengajuan dispensasi DISETUJUI.');
    }

    /**
     * ADMIN: Tolak Pengajuan Ortu
     */
    public function rejectDispensation($id)
    {
        SppPayment::findOrFail($id)->update([
            'dispensation_status' => 'rejected'
        ]);
        return back()->with('warning', 'Pengajuan dispensasi DITOLAK.');
    }
}


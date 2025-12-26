<?php

namespace App\Http\Controllers\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\SppPayment;

class SppOrtuController extends Controller
{
    /**
     * Tampilkan Halaman Tagihan SPP
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil data siswa yang terhubung dengan Akun Ortu ini
        $anak = Student::where('parent_user_id', $user->id)->first();

        if (!$anak) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum terhubung dengan data siswa manapun. Hubungi Admin.');
        }

        // 2. Ambil Tagihan SPP (Published & Urut Terbaru)
        $tagihan_spp = SppPayment::where('student_id', $anak->id)
                                ->where('is_published', true)
                                ->orderBy('year', 'desc')
                                ->orderBy('id', 'desc')
                                ->get();

        return view('ortu.spp.index', compact('anak', 'tagihan_spp'));
    }

    /**
     * Proses Upload Bukti Pembayaran
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'proof_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $payment = SppPayment::findOrFail($id);
        $user = Auth::user();

        // Security Check
        $isMyChild = Student::where('parent_user_id', $user->id)
                            ->where('id', $payment->student_id)
                            ->exists();

        if (!$isMyChild) {
            return back()->with('error', 'Akses ditolak. Ini bukan tagihan anak Anda.');
        }

        if ($request->hasFile('proof_file')) {
            // Hapus file lama jika ada
            if ($payment->proof_file && Storage::disk('public')->exists($payment->proof_file)) {
                Storage::disk('public')->delete($payment->proof_file);
            }

            // Simpan file baru
            $path = $request->file('proof_file')->store('spp-proofs', 'public');

            // Update Status -> Pending Verifikasi
            $payment->update([
                'proof_file' => $path,
                'status' => 'pending',
                'note' => null
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil dikirim. Mohon tunggu verifikasi Admin.');
        }

        return back()->with('error', 'Gagal mengupload file.');
    }

    /**
     * Proses Pengajuan Dispensasi oleh Ortu
     */
    public function storeDispensation(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after:today', // Wajib tanggal masa depan
            'note' => 'required|string|max:255',
        ]);

        $payment = SppPayment::findOrFail($id);
        $user = Auth::user();

        // Security Check
        $isMyChild = Student::where('parent_user_id', $user->id)
                            ->where('id', $payment->student_id)
                            ->exists();

        if (!$isMyChild) {
            return back()->with('error', 'Unauthorized Action.');
        }

        // UPDATE: Set Status ke 'pending' agar Admin bisa review dulu
        $payment->update([
            'dispensation_date' => $request->date,
            'dispensation_note' => $request->note,
            'dispensation_status' => 'pending', // <--- INI PENTING (Agar masuk antrean approval)
        ]);

        return back()->with('success', 'Pengajuan dispensasi dikirim. Menunggu persetujuan Admin.');
    }
}

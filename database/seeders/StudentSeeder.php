<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Mulai seeding students & parents...');

        // 1. Ambil semua kelas
        $classrooms = DB::table('classrooms')->orderBy('level')->orderBy('nama_kelas')->get();

        if ($classrooms->isEmpty()) {
            $this->command->error('❌ Tabel classrooms KOSONG! Jalankan ClassroomSeeder dulu.');
            return;
        }

       // Data Dummy Arrays (Sorted A-Z)



        // 1. Data Dummy Arrays (LENGKAP & SORTED A-Z)

        $maleNames = [
            'Abdul', 'Ahmad', 'Arif', 'Bayu', 'Budi', 'Cahya', 'Cahyo', 'Candra',
            'Darmawan', 'Dodi', 'Dwi', 'Eko', 'Eri', 'Fadli', 'Fajar', 'Firmansyah',
            'Galih', 'Ginanjar', 'Guntur', 'Hadi', 'Hendra', 'Indra', 'Iwan', 'Jaya',
            'Joko', 'Kurniawan', 'Kusnadi', 'Kusuma', 'Lukman', 'Lutfi', 'Maman',
            'Muhammad', 'Nanda', 'Nugroho', 'Oki', 'Omar', 'Prasetyo', 'Purnomo',
            'Rendi', 'Rizki', 'Surya', 'Teguh', 'Umar', 'Wawan', 'Yudi', 'Zainal'
        ];

        $femaleNames = [
            'Aisyah', 'Ani', 'Bella', 'Bunga', 'Cinta', 'Citra', 'Dewi', 'Dian',
            'Eka', 'Elisa', 'Fani', 'Fitri', 'Gadis', 'Gita', 'Hana', 'Hani',
            'Indah', 'Intan', 'Jihan', 'Juli', 'Kamilah', 'Kartika', 'Lestari',
            'Lia', 'Maya', 'Mila', 'Nadia', 'Nurul', 'Oktavia', 'Olivia', 'Puspita',
            'Putri', 'Qori', 'Rani', 'Sari', 'Tika', 'Umi', 'Wulan', 'Yuni', 'Zahra'
        ];

        $lastNames = [
            'Gunawan', 'Halim', 'Hidayat', 'Hutagalung', 'Hutapea', 'Kurniawan',
            'Lumban Gaol', 'Lumban Tobing', 'Manalu', 'Marpaung', 'Mulyadi',
            'Nainggolan', 'Nasution', 'Pardede', 'Pratama', 'Purba', 'Purnomo',
            'Rahman', 'Santoso', 'Saputra', 'Saragih', 'Setiawan', 'Siahaan',
            'Sihite', 'Silitonga', 'Simanjuntak', 'Simbolon', 'Sitompul',
            'Situmorang', 'Susanto', 'Tampubolon', 'Wibowo', 'Wijaya', 'Yulianto'
        ];

        // INI YANG TADI KETINGGALAN (FATHER) - SUDAH URUT A-Z
        $fatherNames = [
            'Abdul', 'Arif', 'Bambang', 'Budi', 'Cahya', 'Candra', 'Darmawan', 'Dwi',
            'Eko', 'Eri', 'Fajar', 'Firmansyah', 'Ginanjar', 'Gunawan', 'Hari',
            'Hendro', 'Irawan', 'Irfan', 'Jaya', 'Joko', 'Kurnia', 'Kusnadi',
            'Lukman', 'Lutfi', 'Maman', 'Mulyono', 'Nanda', 'Nugroho', 'Oki', 'Oman',
            'Pramono', 'Purnomo', 'Rahmat', 'Rudi', 'Suryadi', 'Teguh', 'Usman',
            'Wahyudi', 'Yusuf', 'Zainal'
        ];

        // INI JUGA TADI KETINGGALAN (MOTHER) - SUDAH URUT A-Z
        $motherNames = [
            'Aminah', 'Ani', 'Bella', 'Budiarti', 'Cinta', 'Citra', 'Dewi', 'Dian',
            'Eka', 'Elisa', 'Fani', 'Fitriani', 'Gadis', 'Gita', 'Hani', 'Hartini',
            'Indah', 'Intan', 'Jihan', 'Juli', 'Kartini', 'Kusuma', 'Lestari', 'Lia',
            'Mila', 'Murni', 'Nina', 'Nurhayati', 'Oktaviani', 'Olivia', 'Puspita',
            'Putri', 'Rahayu', 'Ratna', 'Sari', 'Tri', 'Utami', 'Wati', 'Yuliani', 'Zahra'
        ];

        $occupations = [
            'Bidan', 'Buruh', 'Dosen', 'Dokter', 'Driver Online', 'Freelancer',
            'Guru', 'Ibu Rumah Tangga', 'Karyawan Swasta', 'Montir', 'Nelayan',
            'Pedagang', 'Pegawai Negeri', 'Pensiunan', 'Pengusaha', 'Perawat',
            'Petani', 'Teknisi', 'Tukang Bangunan', 'Tukang Kayu', 'Wiraswasta'
        ];

        $religions = [
            'Buddha', 'Hindu', 'Islam', 'Katolik', 'Konghucu', 'Kristen'
        ];

        $lampungDistricts = [
            ['kota' => 'Bandar Lampung',  'kecamatan' => ['Kedaton', 'Sukarame', 'Tanjungkarang Pusat', 'Tanjungkarang Timur', 'Telukbetung Barat', 'Telukbetung Timur']],
            ['kota' => 'Lampung Barat',   'kecamatan' => ['Balik Bukit', 'Liwa', 'Sumber Jaya']],
            ['kota' => 'Lampung Selatan', 'kecamatan' => ['Jati Agung', 'Kalianda', 'Natar', 'Tanjungsari']],
            ['kota' => 'Lampung Tengah',  'kecamatan' => ['Gunung Sugih', 'Seputih Raman', 'Terbanggi Besar']],
            ['kota' => 'Lampung Timur',   'kecamatan' => ['Labuhan Ratu', 'Sukadana', 'Way Jepara']],
            ['kota' => 'Lampung Utara',   'kecamatan' => ['Abung Selatan', 'Abung Timur', 'Kotabumi']],
            ['kota' => 'Metro',           'kecamatan' => ['Metro Barat', 'Metro Pusat', 'Metro Timur']],
            ['kota' => 'Pesisir Barat',   'kecamatan' => ['Krui', 'Pesisir Selatan', 'Pesisir Tengah']],
            ['kota' => 'Pringsewu',       'kecamatan' => ['Adiluwih', 'Pardasuka', 'Pringsewu']],
            ['kota' => 'Tanggamus',       'kecamatan' => ['Kota Agung', 'Talang Padang', 'Wonosobo']],
            ['kota' => 'Tulang Bawang',   'kecamatan' => ['Menggala', 'Tulang Bawang Tengah', 'Tulang Bawang Udik']],
            ['kota' => 'Way Kanan',       'kecamatan' => ['Bahuga', 'Baradatu', 'Blambangan Umpu']],
        ];

        // Kode warna ANSI untuk Terminal: 32m = Hijau, 0m = Reset
        echo "\n\033[32m=== CEK URUTAN FATHER NAMES (Total: " . count($fatherNames) . ") ===\033[0m\n";

        foreach ($fatherNames as $index => $name) {
            $nomor = $index + 1;
            // Format: 1. Nama (enter)
            echo "$nomor. $name\n";
        }

        echo "\n\033[32m=== CEK SELESAI ===\033[0m\n";

        // Mulai counter global
        $totalStudents = 0;

        foreach ($classrooms as $classroom) {
            $tingkat = (int)$classroom->level;

            // Generate 20 students per class
            for ($i = 1; $i <= 20; $i++) {
                $totalStudents++;

                // 1. Generate Basic Info
                $tahunAngkatan = date('Y') - $tingkat + 1;
                $tahunShort = substr($tahunAngkatan, 2, 2);

                // Format NIS & NISN agar urut (Menggunakan totalStudents global)
                $nis = $tahunShort . str_pad($totalStudents, 4, '0', STR_PAD_LEFT);
                $nisn = $tahunAngkatan . str_pad($totalStudents, 8, '0', STR_PAD_LEFT);

                // Cek apakah NIS sudah ada di DB (Untuk mencegah error Duplicate Entry jika seed dijalankan ulang sebagian)
                if (DB::table('students')->where('nis', $nis)->exists()) {
                    continue; // Skip jika sudah ada
                }

                $gender = ($i % 2 == 0) ? 'Perempuan' : 'Laki-laki';
                $firstName = ($gender === 'Laki-laki') ? $maleNames[array_rand($maleNames)] : $femaleNames[array_rand($femaleNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $fullName = $firstName . ' ' . $lastName;

                // Email Unik
                $email = strtolower($firstName . '.' . $lastName . '.' . $nis . '@student.sch.id');
                $email = str_replace(' ', '', $email);

                // Birth Date
                $currentYear = date('Y');
                $birthYear = $currentYear - (6 + $tingkat);
                $birthDate = Carbon::create($birthYear, rand(1, 12), rand(1, 28))->format('Y-m-d');

                // Location
                $location = $lampungDistricts[array_rand($lampungDistricts)];
                $city = $location['kota'];
                $district = $location['kecamatan'][array_rand($location['kecamatan'])];

                // Parents Data
                $fatherName = $fatherNames[array_rand($fatherNames)] . ' ' . $lastName;
                $motherName = $motherNames[array_rand($motherNames)] . ' ' . $lastName;

                // --- DATABASE TRANSACTION ---
                // Kita bungkus dalam transaksi biar kalau error di tengah, gak nyisa data sampah
                DB::transaction(function () use ($fullName, $nis, $email, $fatherName, $classroom, $nisn, $gender, $city, $birthDate, $religions, $district, $occupations, $motherName) {

                    // 2. Buat Akun Student
                    $studentUserId = DB::table('users')->insertGetId([
                        'name' => $fullName,
                        'username' => $nis, // Login pakai NIS
                        'email' => $email,
                        'password' => Hash::make('password123'),
                        'role' => 'student',
                        'email_verified_at' => Carbon::now(),
                        'is_active' => true,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    // 3. Buat Akun Orang Tua (Parent)
                    // Pola Email Ortu: ortu.[nis]@sekolah.com -> PASTI UNIK karena NIS unik
                    $emailOrtu = 'ortu.' . $nis . '@sekolah.com';

                    $parentUserId = DB::table('users')->insertGetId([
                        'name' => $fatherName, // Nama Ayah sebagai nama akun
                        'username' => 'ortu_' . $nis, // Login: ortu_NIS
                        'email' => $emailOrtu,
                        'password' => Hash::make('orangtua123'),
                        'role' => 'parent',
                        'is_active' => true,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    // 4. Buat Profil Siswa (Link ke User Siswa & User Ortu)
                    DB::table('students')->insert([
                        'user_id' => $studentUserId,
                        'classroom_id' => $classroom->id,
                        'parent_user_id' => $parentUserId, // RELASI PENTING
                        'nisn' => $nisn,
                        'nis' => $nis,
                        'nama_lengkap' => $fullName,
                        'jenis_kelamin' => $gender,
                        'tempat_lahir' => $city,
                        'tanggal_lahir' => $birthDate,
                        'agama' => $religions[array_rand($religions)],
                        'nik' => '18' . date('dmy', strtotime($birthDate)) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                        'nomor_telepon' => '08' . rand(12, 15) . rand(1000000, 9999999),
                        'email' => $email,
                        'alamat' => 'Jl. ' . substr($fullName, 0, 5) . ' No. ' . rand(1, 100),
                        'provinsi' => 'Lampung',
                        'kabupaten' => $city,
                        'kecamatan' => $district,
                        'kode_pos' => '35' . rand(100, 999),
                        'status_aktif' => 'Aktif',
                        'nama_ayah' => $fatherName,
                        'pekerjaan_ayah' => $occupations[array_rand($occupations)],
                        'nama_ibu' => $motherName,
                        'pekerjaan_ibu' => (rand(0, 1)) ? $occupations[array_rand($occupations)] : 'Ibu Rumah Tangga',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                });
            }
            $this->command->info("✅ Kelas {$classroom->nama_kelas}: 20 siswa & ortu berhasil dibuat.");
        }

        $this->command->info('🎉 SEEDING SELESAI! Total ' . $totalStudents . ' siswa & akun ortu.');
    }
}

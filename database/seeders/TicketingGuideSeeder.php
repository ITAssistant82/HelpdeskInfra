<?php

namespace Database\Seeders;

use App\Models\Guide;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketingGuideSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin']))->first();

        $guides = [
            [
                'title' => 'Cara Login Menggunakan SSO Microsoft',
                'category' => 'General',
                'order' => 1,
                'content' => '<h3>Login dengan Akun Microsoft</h3>
<p>Ikuti langkah-langkah berikut untuk login ke aplikasi menggunakan SSO Microsoft:</p>
<ol>
<li>Buka halaman <strong>/admin/login</strong></li>
<li>Klik tombol <strong>"Login dengan Microsoft"</strong> (tombol dengan logo Microsoft)</li>
<li>Anda akan diarahkan ke halaman login Microsoft</li>
<li>Masukkan email dan password Microsoft (akun @prasetiyamulya.ac.id)</li>
<li>Setelah login berhasil, Anda akan otomatis kembali ke dashboard</li>
</ol>
<p><em>Catatan: Pastikan email Microsoft Anda sudah terdaftar di sistem. Jika belum, hubungi admin.</em></p>',
                'is_active' => true,
            ],
            [
                'title' => 'Cara Membuat Tiket Baru',
                'category' => 'Incident',
                'order' => 2,
                'content' => '<h3>Membuat Tiket (Incident / Service Request)</h3>
<ol>
<li>Dari menu navigasi kiri, pilih <strong>Ticketing → Tickets</strong></li>
<li>Klik tombol <strong>"Buat Tiket"</strong> (Create) di pojok kanan atas</li>
<li>Isi form tiket:
<ul>
<li><strong>Tipe</strong>: Pilih Incident (masalah) atau Service Request (permintaan)</li>
<li><strong>Kategori</strong>: Pilih kategori yang sesuai dengan masalah/permintaan Anda</li>
<li><strong>Judul</strong>: Tulis judul singkat dan jelas</li>
<li><strong>Deskripsi</strong>: Jelaskan detail masalah atau permintaan Anda</li>
<li><strong>Lokasi</strong>: Pilih lokasi (BSD / Cilandak / Remote / Cloud)</li>
<li><strong>Unit</strong>: Masukkan unit atau departemen Anda</li>
<li><strong>Urgensi</strong>: Tentukan tingkat urgensi (Low / Medium / High / Critical)</li>
</ul>
</li>
<li>Klik <strong>"Simpan"</strong> untuk membuat tiket</li>
</ol>
<p>Tiket Anda akan muncul dengan status <strong>"New"</strong> dan akan segera diproses oleh tim terkait.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Alur Status Tiket',
                'category' => 'General',
                'order' => 3,
                'content' => '<h3>Memahami Alur Status Tiket</h3>
<p>Setiap tiket yang dibuat akan melalui beberapa tahapan status. Berikut penjelasan masing-masing status:</p>
<table class="min-w-full border-collapse border border-gray-300">
<thead>
<tr class="bg-gray-100">
<th class="border border-gray-300 px-4 py-2 text-left">Status</th>
<th class="border border-gray-300 px-4 py-2 text-left">Penjelasan</th>
</tr>
</thead>
<tbody>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded">New</span></td>
<td class="border border-gray-300 px-4 py-2">Tiket baru saja dibuat dan belum ditugaskan</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded">Assigned</span></td>
<td class="border border-gray-300 px-4 py-2">Tiket sudah ditugaskan ke staf atau tim terkait</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">In Progress</span></td>
<td class="border border-gray-300 px-4 py-2">Tiket sedang dalam proses pengerjaan</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded">Pending User</span></td>
<td class="border border-gray-300 px-4 py-2">Menunggu informasi atau konfirmasi dari pengguna</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Pending Approval</span></td>
<td class="border border-gray-300 px-4 py-2">Menunggu persetujuan dari atasan/approver</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded">Pending Vendor</span></td>
<td class="border border-gray-300 px-4 py-2">Menunggu respon atau tindakan dari vendor</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-gray-100 text-gray-800 px-2 py-0.5 rounded">Pending Procurement</span></td>
<td class="border border-gray-300 px-4 py-2">Menunggu proses pengadaan barang/jasa</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded">Escalated</span></td>
<td class="border border-gray-300 px-4 py-2">Tiket dinaikkan ke level atau tim yang lebih tinggi</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded">Solved</span></td>
<td class="border border-gray-300 px-4 py-2">Masalah sudah diatasi dan menunggu konfirmasi penutupan</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-green-100 text-green-800 px-2 py-0.5 rounded">Closed</span></td>
<td class="border border-gray-300 px-4 py-2">Tiket sudah selesai dan ditutup</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Reopened</span></td>
<td class="border border-gray-300 px-4 py-2">Tiket yang sudah selesai dibuka kembali karena masalah belum tuntas</td>
</tr>
<tr>
<td class="border border-gray-300 px-4 py-2"><span class="bg-red-100 text-red-800 px-2 py-0.5 rounded">Rejected/Out of Scope</span></td>
<td class="border border-gray-300 px-4 py-2">Tiket ditolak karena tidak sesuai lingkup layanan</td>
</tr>
</tbody>
</table>',
                'is_active' => true,
            ],
            [
                'title' => 'SLA (Service Level Agreement)',
                'category' => 'General',
                'order' => 4,
                'content' => '<h3>Memahami SLA pada Tiket</h3>
<p>SLA (Service Level Agreement) adalah target waktu penyelesaian tiket berdasarkan tingkat urgensi.</p>
<h4>Indikator SLA:</h4>
<ul>
<li><span class="text-green-600 font-semibold">On Track</span> — Tiket masih dalam batas waktu SLA</li>
<li><span class="text-orange-500 font-semibold">Due Soon</span> — Tiket mendekati batas waktu SLA (perlu perhatian)</li>
<li><span class="text-red-600 font-semibold">Overdue</span> — Tiket sudah melewati batas waktu SLA</li>
<li><span class="text-green-600 font-semibold">Achieved</span> — SLA tercapai (tiket selesai tepat waktu)</li>
</ul>
<h4>Kolom SLA Deadline:</h4>
<p>Menampilkan batas waktu penyelesaian tiket. Staf dapat melihat deadline ini di tabel tiket.</p>
<p><em>Catatan: Kolom SLA dan SLA Deadline hanya terlihat oleh staf/admin.</em></p>',
                'is_active' => true,
            ],
            [
                'title' => 'Fitur Komentar dan Aktivitas',
                'category' => 'General',
                'order' => 5,
                'content' => '<h3>Komentar dan Riwayat Aktivitas</h3>
<h4>Komentar:</h4>
<ul>
<li>Setiap tiket memiliki kolom komentar untuk diskusi antara pengguna dan staf</li>
<li>Staf dapat menambahkan komentar <strong>internal</strong> (hanya terlihat oleh staf lain)</li>
<li>Pengguna hanya bisa melihat komentar publik</li>
</ul>
<h4>Riwayat Aktivitas:</h4>
<ul>
<li>Semua perubahan status, penugasan, dan tindakan tercatat otomatis</li>
<li>Riwayat dapat dilihat di halaman detail tiket pada tab <strong>"Aktivitas"</strong></li>
<li>Memudahkan audit dan pelacakan riwayat penanganan tiket</li>
</ul>',
                'is_active' => true,
            ],
            [
                'title' => 'Fitur Approval (Persetujuan)',
                'category' => 'General',
                'order' => 6,
                'content' => '<h3>Persetujuan Tiket (Approval)</h3>
<p>Beberapa tiket memerlukan persetujuan dari atasan atau approver sebelum dapat diproses.</p>
<h4>Alur Approval:</h4>
<ol>
<li>Tiket masuk ke status <strong>"Pending Approval"</strong></li>
<li>Approver akan menerima notifikasi untuk memberikan persetujuan</li>
<li>Approver dapat:
<ul>
<li><strong>Approve</strong> — Menyetujui tiket untuk diproses</li>
<li><strong>Reject</strong> — Menolak tiket dengan alasan</li>
</ul>
</li>
<li>Jika ada beberapa level approval, tiket akan maju ke approver berikutnya</li>
<li>Setelah semua approval selesai, tiket akan diproses oleh tim terkait</li>
</ol>
<p>Progres persetujuan dapat dilihat di halaman detail tiket.</p>',
                'is_active' => true,
            ],
            [
                'title' => 'Lampiran dan Upload File',
                'category' => 'General',
                'order' => 7,
                'content' => '<h3>Menambahkan Lampiran ke Tiket</h3>
<p>Anda dapat menambahkan file pendukung saat membuat atau mengedit tiket.</p>
<h4>Cara Upload:</h4>
<ol>
<li>Saat membuat tiket, scroll ke bagian <strong>"Lampiran"</strong></li>
<li>Klik area upload atau drag & drop file</li>
<li>File yang didukung: gambar, PDF, dokumen (max 20MB per file)</li>
<li>Klik <strong>"Simpan"</strong> untuk menyimpan tiket beserta lampiran</li>
</ol>
<p><em>Catatan: Lampiran yang sudah diupload dapat dilihat di halaman detail tiket.</em></p>',
                'is_active' => true,
            ],
            [
                'title' => 'Notifikasi Tiket',
                'category' => 'General',
                'order' => 8,
                'content' => '<h3>Notifikasi Tiket</h3>
<p>Sistem akan mengirimkan notifikasi untuk berbagai event terkait tiket Anda.</p>
<h4>Jenis Notifikasi:</h4>
<ul>
<li><strong>Tiket Baru</strong> — Diberitahukan saat ada tiket baru yang membutuhkan penanganan</li>
<li><strong>Perubahan Status</strong> — Notifikasi saat status tiket berubah</li>
<li><strong>Komentar Baru</strong> — Ada komentar baru pada tiket Anda</li>
<li><strong>Menunggu Persetujuan</strong> — Tiket membutuhkan approval Anda</li>
<li><strong>SLA Warning</strong> — Peringatan SLA mendekati deadline</li>
</ul>
<h4>Cara Melihat Notifikasi:</h4>
<ul>
<li>Ikon lonceng di pojok kanan atas dashboard menampilkan notifikasi</li>
<li>Notifikasi otomatis ter-update setiap 30 detik</li>
<li>Klik notifikasi untuk langsung menuju tiket terkait</li>
</ul>',
                'is_active' => true,
            ],
        ];

        foreach ($guides as $guide) {
            Guide::create(array_merge($guide, [
                'created_by' => $admin?->id ?? 1,
            ]));
        }

        $this->command->info('Seeder panduan tiket berhasil dijalankan!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Note;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a default test user
        User::factory()->create([
            'name' => 'Aura User',
            'email' => 'user@aurapad.com',
        ]);

        // Create standard categories
        $work = Category::create([
            'name' => 'Pekerjaan',
            'color' => 'blue',
            'icon' => '💼',
        ]);

        $ideas = Category::create([
            'name' => 'Ide & Kreatif',
            'color' => 'amber',
            'icon' => '💡',
        ]);

        $personal = Category::create([
            'name' => 'Pribadi',
            'color' => 'emerald',
            'icon' => '🏠',
        ]);

        $todo = Category::create([
            'name' => 'Daftar Tugas',
            'color' => 'rose',
            'icon' => '✅',
        ]);

        // Seed Note 1: Welcome
        Note::create([
            'title' => '✨ Selamat Datang di AuraPad!',
            'color' => 'purple',
            'is_pinned' => true,
            'category_id' => $ideas->id,
            'content' => "# Selamat Datang di AuraPad Pro! 🚀\n\nAuraPad adalah aplikasi catatan digital modern yang dirancang untuk memberikan kenyamanan dan estetika premium saat Anda menulis pikiran, ide, tugas, atau catatan kerja.\n\n### Fitur Utama AuraPad:\n1. **Penyimpanan Otomatis (Autosave)**: Anda tidak perlu khawatir kehilangan tulisan. Setiap kali Anda mengetik, sistem akan menyimpannya secara otomatis ke database.\n2. **Pratinjau Markdown (Live Preview)**: Tulis catatan Anda dengan format Markdown yang rapi dan lihat hasilnya secara instan.\n3. **Kategori Kustom**: Kelompokkan catatan Anda ke dalam kategori seperti 💼 Pekerjaan, 💡 Ide, atau 🏠 Pribadi dengan warna penanda.\n4. **Warna Catatan**: Berikan nuansa warna pastel yang menenangkan pada kartu catatan Anda.\n5. **Sematan & Arsip (Pin & Archive)**: Sematkan catatan penting agar selalu berada di posisi paling atas, atau arsipkan catatan lama agar bilah sisi tetap rapi.\n6. **Ekspor Mudah**: Unduh catatan Anda dalam format teks polos (`.txt`) atau Markdown (`.md`) dengan satu klik.\n\n---\n\n### Cara Memulai:\n* Klik tombol **\"+\" Catatan Baru** di bilah sisi kiri untuk membuat catatan baru.\n* Tulis judul dan isi di panel editor sebelah kanan.\n* Klik tombol **Preview** di bagian kanan atas untuk melihat hasil render format ini.\n* Ubah warna latar belakang catatan Anda menggunakan pemilih palet warna di baris aksi.\n\n*Selamat menulis! Semoga AuraPad membantu meningkatkan produktivitas Anda.* ✍️",
        ]);

        // Seed Note 2: Markdown guide
        Note::create([
            'title' => '📝 Panduan Cepat Markdown',
            'color' => 'emerald',
            'is_pinned' => false,
            'category_id' => $work->id,
            'content' => "# Panduan Cepat Markdown 🎨\n\nAuraPad mendukung penuh penulisan Markdown. Berikut adalah panduan cepat sintaksis yang bisa langsung Anda gunakan:\n\n## 1. Judul (Headers)\nGunakan simbol pagar `#` di awal baris:\n# Judul Besar (H1)\n## Judul Sedang (H2)\n### Judul Kecil (H3)\n\n## 2. Format Teks\n* *Cetak miring* dengan bintang: `*Teks Miring*`\n* **Cetak tebal** dengan dua bintang: `**Teks Tebal**`\n* ***Cetak tebal & miring*** dengan tiga bintang: `***Tebal & Miring***`\n* ~~Tercoret~~ dengan dua tilde: `~~Tercoret~~`\n\n## 3. Daftar (Lists)\nDaftar Peluru (Unordered List):\n* Item Satu\n* Item Dua\n  * Sub-item\n\nDaftar Bernomor (Ordered List):\n1. Langkah Pertama\n2. Langkah Kedua\n\nDaftar Tugas (Checklist):\n- [x] Tugas yang sudah selesai\n- [ ] Tugas yang belum selesai\n\n## 4. Kutipan & Kode\nKutipan (Blockquote):\n> \"Menulislah dengan bebas, rapikan kemudian.\" - Kutipan Bijak\n\nBlok Kode (Code Block):\n```javascript\n// Contoh kode JavaScript\nconst sapa = () => {\n    console.log(\"Halo dari AuraPad!\");\n};\nsapa();\n```\n\n## 5. Tabel\n| Nama Kategori | Warna | Ikon |\n| :--- | :---: | :---: |\n| Pekerjaan | Biru | 💼 |\n| Ide | Kuning | 💡 |\n| Pribadi | Hijau | 🏠 |",
        ]);

        // Seed Note 3: Keyboard shortcuts
        Note::create([
            'title' => '⌨️ Pintasan Keyboard',
            'color' => 'amber',
            'is_pinned' => false,
            'category_id' => $personal->id,
            'content' => "# Pintasan Keyboard AuraPad ⌨️\n\nUntuk mempercepat navigasi dan alur kerja Anda, gunakan pintasan keyboard berikut saat mengedit catatan:\n\n| Tombol | Fungsi |\n| :--- | :--- |\n| `Ctrl` + `N` | Membuat catatan baru secara instan |\n| `Ctrl` + `S` | Memaksa penyimpanan catatan saat itu juga |\n| `Ctrl` + `P` | Menyematkan / melepas sematan catatan aktif |\n| `Ctrl` + `A` | Mengarsipkan catatan aktif |\n| `Ctrl` + `E` | Mengunduh catatan aktif sebagai file Markdown (`.md`) |\n| `Ctrl` + `Alt` + `P` | Beralih antara mode Edit dan Preview |\n\n---\n\n*Tip: Semua pintasan di atas dirancang untuk meminimalkan ketergantungan Anda pada mouse, membuat pengalaman menulis terasa lebih lancar.*",
        ]);
    }
}

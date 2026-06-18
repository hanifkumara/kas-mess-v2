# 💰 Kas Mess

Aplikasi pengelolaan kas mess/kost berbasis web — mencatat iuran bulanan anggota,
status pembayaran, pengeluaran (dikelompokkan per batch), saldo kas, dan laporan
bulanan dengan tampilan modern.

Dibangun dengan **Laravel 13**, **Blade**, **Tailwind CSS v4**, **Alpine.js**, dan
**SQLite** (struktur tetap kompatibel dengan MySQL). Tanpa Docker.

## ✨ Fitur

- **Dashboard** — ringkasan periode aktif: kas awal, iuran, sudah/belum dibayar,
  pengeluaran, saldo akhir, progress bar, dan status iuran tiap anggota.
- **Manajemen Anggota** — CRUD anggota (nama, aktif/nonaktif, catatan).
- **Manajemen Periode Kas** — CRUD periode (nama, bulan, tahun, iuran per anggota,
  kas awal, status aktif/arsip). Hanya satu periode aktif.
- **Pembayaran Iuran** — tandai lunas/belum lunas, tanggal bayar, nominal, metode,
  catatan. Tombol cepat: *Tandai Lunas*, *Batalkan*, *Tandai Semua Lunas*.
- **Pengeluaran & Batch** — CRUD pengeluaran (item, kategori, harga, tanggal, batch).
  Kategori: Air, Listrik, Beras, Gas, Sabun, IPL, Lainnya.
- **Laporan Bulanan** — pemasukan, pengeluaran per batch dengan *running balance*
  (saldo sebelum & sisa saldo), tombol **Print**, dan **Export CSV**.
- **UI/UX** — sidebar + topbar, kartu statistik, badge status, empty state, toast
  notification, modal konfirmasi hapus, desain responsive, format rupiah & tanggal
  Indonesia.

## 🧮 Rumus perhitungan

```
Saldo Akhir = Kas Awal + Total Iuran Dibayar − Total Pengeluaran
```

Semua angka dihitung otomatis oleh `App\Services\CashReportService` dari data,
bukan di-hardcode. Running balance per batch dihitung berurutan dari pemasukan
(kas awal + iuran) dikurangi tiap batch sesuai urutan.

## 🚀 Menjalankan aplikasi

Prasyarat: PHP 8.3+, Composer, Node.js (untuk build aset).

```bash
# 1. Install dependency
composer install
npm install

# 2. Siapkan environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite) + data contoh Juni 2026
php artisan migrate --seed

# 4. Build aset frontend
npm run build      # produksi (sekali)
# atau: npm run dev   # hot-reload saat development

# 5. Jalankan server
php artisan serve
```

Buka http://localhost:8000.

> Saat development, jalankan `npm run dev` bersamaan dengan `php artisan serve`
> agar Tailwind/Vite ter-update otomatis.

## 🗄️ Struktur database

| Tabel            | Fungsi |
| ---------------- | ------ |
| `members`        | Anggota (nama, is_active, notes) |
| `cash_periods`   | Periode kas (nama, bulan, tahun, iuran, kas awal, is_active) |
| `payments`       | Iuran per anggota per periode (amount, status, paid_at, method) |
| `expense_batches`| Kelompok pengeluaran (title, batch_date, sort_order) |
| `expenses`       | Item pengeluaran (item_name, category, amount, expense_date) |

## 🧱 Pindah ke MySQL (opsional)

Struktur sudah kompatibel. Cukup ubah `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kas_mess
DB_USERNAME=root
DB_PASSWORD=secret
```

Lalu `php artisan migrate --seed`.

## 📂 Struktur kode penting

```
app/
├── Http/Controllers/   DashboardController, MemberController, CashPeriodController,
│                       PaymentController, ExpenseController, ExpenseBatchController, ReportController
├── Http/Requests/      MemberRequest, CashPeriodRequest, PaymentRequest, ExpenseRequest, ExpenseBatchRequest
├── Models/             Member, CashPeriod, Payment, ExpenseBatch, Expense
├── Services/
│   └── CashReportService.php   # seluruh perhitungan laporan & ringkasan
└── helpers.php         # rp(), rp_plain(), parse_rp(), tgl()

resources/views/
├── components/         layout, card, stat-card, status-badge, button, confirm-delete, ...
├── dashboard/          index.blade.php, empty.blade.php
├── members/ periods/ payments/ expenses/ batches/ reports/
```

## 🔧 Tech stack

- Laravel 13 (PHP 8.4+)
- Blade + komponen anonim
- Tailwind CSS v4 (via Vite) dengan palet navy kustom
- Alpine.js (sidebar mobile, modal, toast)
- SQLite (default) / MySQL (kompatibel)

## 🔐 Autentikasi

Aplikasi dilindungi login (guard `admins`). Role: `superadmin` & `resident`.
Akun contoh (seeder):

| Email | Password | Role |
| --- | --- | --- |
| hanif@jelita.com | `password` | superadmin |
| henry@jelita.com | `password` | resident |

> Ganti password setelah deploy produksi.

## 📥 Import / Export Pengeluaran

- **Import CSV**: halaman Pengeluaran → tombol "📥 Import CSV". Header wajib:
  `date, item_name, category, amount`. Setiap tanggal unik dijadikan satu batch.
- **Template CSV**: tersedia tombol unduh di halaman import.
- **Export CSV laporan**: di halaman Laporan → "⬇ Export CSV".
- Implementasi native (tanpa `maatwebsite/excel`) agar kompatibel dengan PHP 8.5.

## 🚢 Produksi (mess-jelita)

Deploy native (tanpa Docker) di server Ubuntu 24.04:

- **URL**: https://mess-jelita.hnifkumara.com
- **Server**: `biznet_gio` (103.93.129.109), user `kumara`
- **Path**: `~/apps/kas-mess-v2`
- **Stack**: PHP 8.4-FPM (ondrej PPA) + nginx + SQLite, lewat Cloudflare proxy
- **nginx**: `/etc/nginx/sites-available/mess-jelita.hnifkumara.com`
- **SSL**: Let's Encrypt via certbot (auto-renew)

Update kode (di server):

```bash
cd ~/apps/kas-mess-v2 && ./deploy.sh
```

`deploy.sh` menjalankan: `git pull` → `composer install` → `npm run build` →
`migrate` → cache config/route/view/event → fix permission → reload php-fpm.


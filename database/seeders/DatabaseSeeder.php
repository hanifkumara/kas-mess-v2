<?php

namespace Database\Seeders;

use App\Models\CashPeriod;
use App\Models\Expense;
use App\Models\ExpenseBatch;
use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedJune2026();
    }

    private function seedJune2026(): void
    {
        // 1) Anggota (urutan sesuai daftar)
        $memberNames = ['Henry', 'Rizky', 'Hanif', 'Ahrul', 'Tarom', 'Yusuf', 'Gaza', 'Allam', 'Raihan'];
        $members = [];
        foreach ($memberNames as $name) {
            $members[$name] = Member::firstOrCreate(
                ['name' => $name],
                ['is_active' => true, 'notes' => null],
            );
        }

        // 2) Periode kas Juni 2026
        $period = CashPeriod::firstOrCreate(
            ['month' => 6, 'year' => 2026],
            [
                'name' => 'Juni 2026',
                'monthly_due' => 300000,
                'starting_balance' => 3000000,
                'is_active' => true,
            ],
        );
        // Pastikan hanya satu periode aktif
        CashPeriod::whereKeyNot($period)->update(['is_active' => false]);
        $period->update(['is_active' => true]);

        // 3) Pembayaran iuran: semua lunas kecuali Rizky
        $paidDate = '2026-06-02';
        foreach ($members as $name => $member) {
            $isPaid = $name !== 'Rizky';
            Payment::updateOrCreate(
                ['cash_period_id' => $period->id, 'member_id' => $member->id],
                [
                    'amount' => $isPaid ? $period->monthly_due : 0,
                    'status' => $isPaid ? 'paid' : 'unpaid',
                    'paid_at' => $isPaid ? $paidDate : null,
                    'method' => $isPaid ? 'Tunai' : null,
                    'notes' => null,
                ],
            );
        }

        // 4) Batch pengeluaran + item (sesuai screenshot)
        $batches = [
            [
                'title' => 'Batch 1', 'batch_date' => '2026-06-05', 'sort_order' => 1,
                'items' => [
                    ['Air Galon 2', 'Air', 43000],
                    ['Kursi Gaming 3', 'Lainnya', 90000],
                    ['Tisu 30 pack', 'Lainnya', 131199],
                    ['Beras 5 KG', 'Beras', 75000],
                    ['Listrik Monthly', 'Listrik', 1020370],
                ],
            ],
            [
                'title' => 'Batch 2', 'batch_date' => '2026-06-12', 'sort_order' => 2,
                'items' => [
                    ['Air Galon 2', 'Air', 43000],
                    ['Gas LPG 3kg', 'Gas', 23000],
                    ['Mas Ambar minggu ke 2', 'Lainnya', 180000],
                    ['Sabun Cuci Piring', 'Sabun', 22000],
                ],
            ],
            [
                'title' => 'Batch 3', 'batch_date' => '2026-06-19', 'sort_order' => 3,
                'items' => [
                    ['IPL dan AIR', 'IPL', 824480],
                    ['Beras + Minyak Goreng', 'Beras', 119000],
                    ['Air Galon 2', 'Air', 43000],
                    ['Minyak Goreng 1 Liter', 'Lainnya', 23900],
                    ['Beras 5 KG', 'Beras', 89900],
                ],
            ],
        ];

        foreach ($batches as $b) {
            $batch = ExpenseBatch::firstOrCreate(
                ['cash_period_id' => $period->id, 'title' => $b['title']],
                [
                    'batch_date' => $b['batch_date'],
                    'sort_order' => $b['sort_order'],
                    'notes' => null,
                ],
            );

            foreach ($b['items'] as [$item, $category, $amount]) {
                Expense::firstOrCreate(
                    [
                        'cash_period_id' => $period->id,
                        'expense_batch_id' => $batch->id,
                        'item_name' => $item,
                    ],
                    [
                        'category' => $category,
                        'amount' => $amount,
                        'expense_date' => $b['batch_date'],
                        'notes' => null,
                    ],
                );
            }
        }
    }
}

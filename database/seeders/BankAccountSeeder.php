<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bankAccounts = [
            [
                'nama_bank' => 'BCA',
                'nama' => 'FOSJABAR',
                'no_rek' => '1234567890',
            ],
            [
                'nama_bank' => 'Mandiri',
                'nama' => 'FOSJABAR',
                'no_rek' => '0987654321',
            ],
            [
                'nama_bank' => 'BNI',
                'nama' => 'FOSJABAR',
                'no_rek' => '5678901234',
            ],
        ];

        foreach ($bankAccounts as $account) {
            BankAccount::create($account);
        }
    }
}

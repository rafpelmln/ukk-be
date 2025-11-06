<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('nama_bank')->get();

        return response()->json([
            'success' => true,
            'data' => $bankAccounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'nama_bank' => $account->nama_bank,
                    'nama' => $account->nama,
                    'no_rek' => $account->no_rek,
                    'photo_url' => $account->photo_url,
                ];
            }),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TransactionHistory;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    //
    public function index(){
        $transactions = TransactionHistory::orderBy('id', 'desc')->get();
        return view('transactions', compact('transactions'));
    }
}

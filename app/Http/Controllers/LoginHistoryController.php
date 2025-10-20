<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    public function index(Request $request)
    {
        $loginHistories = LoginHistory::with('user')
            ->latest()
            ->paginate(15);

        return view('login-history.index', compact('loginHistories'));
    }
}

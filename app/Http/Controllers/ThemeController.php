<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function set(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,device'
        ]);

        session(['theme' => $request->theme]);
        return response()->json(['success' => true]);
    }
}

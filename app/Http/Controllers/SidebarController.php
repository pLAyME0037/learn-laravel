<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SidebarController extends Controller
{
    public function toggle()
    {
        $collapsed = !session('sidebar_collapsed', false);
        session(['sidebar_collapsed' => $collapsed]);

        return response()->json([
            'success' => true,
            'collapsed' => $collapsed
        ]);
    }
}

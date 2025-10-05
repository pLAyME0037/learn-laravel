<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        // Ensure theme session is set
        if (!session()->has('theme')) {
            session(['theme' => 'device']);
        };

        return view('dashboard', [
            'sidebarCollapsed' => session('sidebar_collapsed', false)
        ]);
    }
}

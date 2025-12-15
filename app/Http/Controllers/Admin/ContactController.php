<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Update Emergency Contact specifically.
     */
    public function updateEmergency(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'relation' => 'required|string',
        ]);

        $student->contactDetail()->updateOrCreate(
            [], // Search condition (empty means update current or create)
            [
                'emergency_name' => $validated['name'],
                'emergency_phone' => $validated['phone'],
                'emergency_relation' => $validated['relation'],
            ]
        );

        return back()->with('success', 'Emergency contact updated.');
    }
}
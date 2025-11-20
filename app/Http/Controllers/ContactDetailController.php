<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Models\ContactDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view.contact-details')
            ->only('index', 'show');
        $this->middleware('permission:create.contact-details')
            ->only('create', 'store');
        $this->middleware('permission:edit.contact-details')
            ->only('edit', 'update');
        $this->middleware('permission:delete.contact-details')
            ->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $contactDetails = ContactDetail::with(['user', 'student', 'instructor'])
            ->paginate(10)
            ->through(function ($contactDetail) {
                $contactDetail->id = $contactDetail->person_id; // Explicitly add 'id' key
                return $contactDetail;
            });

        return view('admin.contact_details.index', compact('contactDetails'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.contact_details.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contactable_type' => 'required|string|max:255',
            'contactable_id'   => 'required|integer',
            'phone'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'state'            => 'nullable|string|max:255',
            'zip_code'         => 'nullable|string|max:10',
            'country'          => 'nullable|string|max:255',
        ]);

        ContactDetail::create($validated);

        return redirect()->route('admin.contact-details.index')
        ->with('success', 'Contact detail created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactDetail $contactDetail): View
    {
        return view('admin.contact_details.show', compact('contactDetail'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactDetail $contactDetail): View
    {
        return view('admin.contact_details.edit', compact('contactDetail'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactDetail $contactDetail): RedirectResponse
    {
        $validated = $request->validate([
            'contactable_type' => 'required|string|max:255',
            'contactable_id'   => 'required|integer',
            'phone'            => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:255',
            'address'          => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:255',
            'state'            => 'nullable|string|max:255',
            'zip_code'         => 'nullable|string|max:10',
            'country'          => 'nullable|string|max:255',
        ]);

        $contactDetail->update($validated);

        return redirect()->route('admin.contact-details.show', $contactDetail)
        ->with('success', 'Contact detail updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactDetail $contactDetail): RedirectResponse
    {
        $contactDetail->delete();

        return redirect()->route('admin.contact-details.index')
        ->with('success', 'Contact detail deleted successfully.');
    }
}

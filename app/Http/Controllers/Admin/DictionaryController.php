<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dictionary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DictionaryController extends Controller
{
    public function index()
    {
        // Group by category for a cleaner UI view
        $dictionaries = Dictionary::orderBy('category')
            ->orderBy('key')
            ->get()
            ->groupBy('category');

        return view('admin.dictionaries.index', compact('dictionaries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'key'      => [
                'required', 
                'string', 
                'max:50', 
                // Unique combo of category + key
                Rule::unique('dictionaries')->where(fn ($q) => $q->where('category', $request->category))
            ],
            'label'    => 'required|string|max:100',
            'is_active' => 'boolean'
        ]);

        Dictionary::create($validated);

        return redirect()->back()->with('success', 'Dictionary item added.');
    }

    public function update(Request $request, Dictionary $dictionary)
    {
        $validated = $request->validate([
            'label'     => 'required|string|max:100',
            'is_active' => 'boolean'
            // We usually don't allow changing 'key' or 'category' to prevent data orphan issues
        ]);

        $dictionary->update($validated);

        return redirect()->back()->with('success', 'Item updated.');
    }

    public function destroy(Dictionary $dictionary)
    {
        $dictionary->delete();
        return redirect()->back()->with('success', 'Item deleted.');
    }
}
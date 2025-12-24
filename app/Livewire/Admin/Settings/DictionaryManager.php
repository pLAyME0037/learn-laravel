<?php
namespace App\Livewire\Admin\Settings;

use App\Models\Dictionary;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class DictionaryManager extends Component
{
                                          // State
    public $selectedCategory  = 'gender'; // Default tab
    public $categories        = [];       // List of all available categories
    public $showModal         = false;
    public $isEditing         = false;
    public $newCategoryName   = '';
    public $showCategoryModal = false;

    // Form Data
    public $id, $category, $key, $label, $is_active = true;

    public function mount()
    {
        $this->refreshCategories();
        // If the DB is empty, set a default category to prevent UI blankness
        if (empty($this->categories)) {
            $this->categories = ['general', 'gender', 'academic_status'];
            $this->category   = 'general';
        }
    }

    public function refreshCategories()
    {
        // Get unique categories currently in DB
        $dbCategories = Dictionary::distinct()->pluck('category')->toArray();
        // Merge with defaults to ensure core system categories always appear
        $defaults = [
            'gender', 'academic_status', 'enrollment_status', 'title',
        ];
        $this->categories = array_unique(array_merge($defaults, $dbCategories));
        sort($this->categories);
    }

    public function selectCategory($cat)
    {
        $this->selectedCategory = $cat;
        $this->resetValidation();
    }

    public function create()
    {
        $this->reset(['id', 'key', 'label', 'is_active']);
        $this->category  = $this->selectedCategory;
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function edit(Dictionary $dict)
    {
        $this->id        = $dict->id;
        $this->category  = $dict->category;
        $this->key       = $dict->key;
        $this->label     = $dict->label;
        $this->is_active = $dict->is_active;

        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'category'  => 'required|string|max:50',
            'key'       => [
                'required', 'string', 'max:50',
                // Unique key per category, ignoring current record if editing
                Rule::unique('dictionaries')->where(fn($q) =>
                    $q->where('category', $this->category))->ignore($this->id),
            ],
            'label'     => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        Dictionary::updateOrCreate(
            ['id' => $this->id],
            [
                'category'  => $this->category,
                'key'       => $this->key,
                'label'     => $this->label,
                'is_active' => $this->is_active,
            ]
        );

        $this->showModal = false;
        $this->refreshCategories(); // In case a new category was added
        $this->dispatch('notify', 'Item saved successfully.');
    }

    public function createCategory()
    {
        $this->reset('newCategoryName');
        $this->showCategoryModal = true;
    }

    public function saveCategory()
    {
        $this->validate(['newCategoryName' => 'required|string|min:3|unique:dictionaries,category']);
        $this->categories[]      = strtolower(str_replace(' ', '_', trim($this->newCategoryName)));
        $this->selectedCategory  = end($this->categories);
        $this->showCategoryModal = false;
        $this->create();
    }

    public function delete($id)
    {
        Dictionary::find($id)->delete();
        $this->dispatch('notify', 'Item deleted.');
    }

    #[Layout('layouts.app', ['header' => 'System Dictionaries'])]
    public function render()
    {
        $groupedItems = Dictionary::orderBy('key')
            ->get()
            ->groupBy('category');

        return view('livewire.admin.settings.dictionary-manager', [
            'groupedItems' => $groupedItems,
        ]);
    }
}

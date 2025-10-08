<?php
namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextareaInput extends Component
{
    public $name;
    public $label;
    public $value;
    public $placeholder;
    public $required;
    public $rows;
    public $disabled;
    public $readonly;
    public $error;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $name,
        $label = null,
        $value = null,
        $placeholder = null,
        $required = false,
        $rows = 4,
        $disabled = false,
        $readonly = false,
        $error = null
    ) {
        $this->name        = $name;
        $this->label       = $label;
        $this->value       = old($name, $value);
        $this->placeholder = $placeholder;
        $this->required    = $required;
        $this->rows        = $rows;
        $this->disabled    = $disabled;
        $this->readonly    = $readonly;
        $this->error       = $error;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.textarea-input');
    }
}

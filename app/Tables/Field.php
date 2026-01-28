<?php
namespace App\Tables;

use Illuminate\Support\Str;

class Field
{
    public string $type         = 'text'; // text, image, badge, action
    public ?string $key         = null;   // database column e.g. 'user.name'
    public string $label        = '';
    public string $css          = '';
    public $formatCallback      = null;
    public $componentName       = null;
    public $viewDataCallback    = null;
    public $componentAttributes = [];
    public array $actions       = [];

    public static function make(string $key): self {
        $instance       = new self();
        $instance->key  = $key;
        $instance->type = 'text';
        return $instance;
    }

    /**
     * defined the view and optional callback
     * @param string $viewName Blade view path
     * @param callable|null $dataCallback fn($value, $row) => ['extra' => 'data']
     */
    public function view(string $viewName, callable $dataCallback = null): self {
        $this->type = 'view';
        $this->componentName = $viewName; // We reuse this property
        $this->viewDataCallback = $dataCallback;
        return $this;
    }

    public function component(string $name, callable $attributesCallback): self {
        $this->type           = 'component';
        $this->componentName  = $name;
        $this->formatCallback = $attributesCallback; // Use callback to generate props per row
        return $this;
    }

    public static function index(): self {
        $instance       = new self();
        $instance->type = 'index'; // Special type
        $instance->key  = null;
        return $instance;
    }

    public function html(callable $callback): self {
        $this->type           = 'html';
        $this->formatCallback = $callback;
        return $this;
    }

    public static function image(string $key): self {
        $instance       = new self();
        $instance->key  = $key;
        $instance->type = 'image';
        return $instance;
    }

    /**
     * Set the field type explicitly (e.g. 'actions', 'badge', 'image').
     */
    public function type(string $type): self {
        $this->type = $type;
        return $this;
    }

    public function actions(array $actions): self {
        $this->type    = 'actions';
        $this->actions = $actions;
        return $this;
    }

    public static function badge(string $key): self {
        $instance       = new self();
        $instance->key  = $key;
        $instance->type = 'badge';
        return $instance;
    }

    public function label(string $label): self {
        $this->label = $label;
        return $this;
    }

    public function css(string $classes): self {
        $this->css = $classes;
        return $this;
    }

    public function format(callable $callback): self {
        $this->formatCallback = $callback;
        return $this;
    }

    public function bold(): self {
        $this->css .= ' font-bold text-gray-900 dark:text-white ';
        return $this;
    }

    public function small(): self {
        $this->css .= ' text-xs text-gray-500 ';
        return $this;
    }

    public function upper(): self {
        $this->formatCallback = fn($v) => Str::upper($v);
        return $this;
    }

    public function resolve($row) {
        if ($this->type === 'index') {
            return ''; // Return empty string, View handles the number
        }

        $value = $this->key ? data_get($row, $this->key) : $row;

        if ($this->formatCallback) {
            // Pass value AND the whole row for complex logic
            return call_user_func($this->formatCallback, $value, $row);
        }

        if (is_null($value) && $this->type !== 'actions') {
            return 'N/A';
        }

        if ($this->label && $value && $value !== 'N/A') {
            return $this->label . $value;
        }
        return $value;
    }

    public function resolveViewData($row) {
        $value = $this->resolve($row);

        $defaultData = ['value' => $value, 'row' => $row];
        if ($this->viewDataCallback) {
            $extraData = call_user_func($this->viewDataCallback, $value, $row);
            return array_merge($defaultData, $extraData);
        }
        return $defaultData;
    }
}

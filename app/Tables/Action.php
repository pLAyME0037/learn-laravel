<?php
namespace App\Tables;

class Action
{
    public $urlCallback = null; // Closure to generate URL
    public $iconCallback;
    public $colorCallback;
    public $conditionCallback;
    public string $type = 'button'; // 'button' or 'link'
    public string $label;
    public string $method; // Livewire method to call
    public string $icon           = '';
    public string $color          = 'text-gray-500 hover:text-gray-700';
    public bool $confirm          = false;
    public string $confirmMessage = 'Are you sure?';

    public static function make(string $method): self
    {
        $instance         = new self();
        $instance->method = $method;
        return $instance;
    }

    // Factory for Links
    public static function link(callable $urlCallback): self
    {
        $instance              = new self();
        $instance->type        = 'link';
        $instance->urlCallback = $urlCallback;
        return $instance;
    }

    public static function button(string $method): self
    {
        $instance         = new self();
        $instance->type   = 'button';
        $instance->method = $method;
        return $instance;
    }

    public function resolveUrl($row)
    {
        if (is_callable($this->urlCallback)) {
            return call_user_func($this->urlCallback, $row);
        }
        return '#';
    }

    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function when(callable $callback): self
    {
        $this->conditionCallback = $callback;
        return $this;
    }

    public function shouldRender($row): bool
    {
        if ($this->conditionCallback) {
            return call_user_func($this->conditionCallback, $row);
        }
        return true; // Default: Always show
    }

    public function icon(callable | string $icon): self
    {
        $this->iconCallback = is_callable($icon) ? $icon : fn() => $icon;
        return $this;
    }

    public function color(callable | string $color): self
    {
        $this->colorCallback = is_callable($color) ? $color : fn() => $color;
        return $this;
    }

    // Resolve methods
    public function resolveIcon($row)
    {
        return call_user_func($this->iconCallback, $row);
    }

    public function resolveColor($row)
    {
        return call_user_func($this->colorCallback, $row);
    }

    public function confirm(string $message = 'Are you sure?'): self
    {
        $this->confirm        = true;
        $this->confirmMessage = $message;
        return $this;
    }
}

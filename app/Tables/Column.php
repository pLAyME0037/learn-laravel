<?php
namespace App\Tables;

class Column
{
    public string $header;
    public array $fields  = [];
    public string $align  = 'left';
    public bool $sortable = false;
    public string $width  = '';

    public static function make(string $header): self
    {
        $instance         = new self();
        $instance->header = $header;
        return $instance;
    }

    // Default vertical stack
    public function stack(array $fields): self
    {
        $this->fields[] = ['type' => 'stack', 'items' => $fields];
        return $this;
    }

    // Horizontal Row (Flex)
    public function row(array $fields): self
    {
        $this->fields[] = ['type' => 'row', 'items' => $fields];
        return $this;
    }

    /**
     * @param int|string $cols Number of columns OR Tailwind/CSS grid-template-columns value
     */
    public function grid($cols, array $items): self
    {
        // If integer, convert to repeat(N, 1fr) style logic later, or keep simple class
        // If string, use as custom style/class

        $this->fields[] = [
            'type'   => 'grid',
            'config' => $cols, // Store raw config
            'items'  => $items,
        ];
        return $this;
    }

    // UI Helpers
    public function center(): self
    {
        $this->align = 'center';
        return $this;
    }
    public function right(): self
    {
        $this->align = 'right';
        return $this;
    }
    public function sortable(): self
    {
        $this->sortable = true;
        return $this;
    }
    public function width(string $widthClass): self
    {
        $this->width = $widthClass;
        return $this;
    }
}

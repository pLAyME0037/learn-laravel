<?php

namespace App\Tables;

use Illuminate\Database\Eloquent\Builder;

class Table
{
    protected array $columns = [];
    protected array $hiddenColumns = [];
    protected Builder $query;
    protected string $style = 'full'; // minimal, compact, full
    protected int $perPage  = 10;

    private function __construct(Builder $query) {
        $this->query = $query;
    }

    public static function make(Builder $query): self {
        return new self($query);
    }

    public function getColumns(): array {
        return $this->columns;
    }

    public function getHiddenColumns(): array {
        return $this->hiddenColumns;
    }

    public function getQuery(): Builder {
        return $this->query;
    }

    public function getPerPage(): int {
        return $this->perPage;
    }

    public function columns(array $columns): self {
        $this->columns = $columns;
        return $this;
    }

    public function hide(string $header) {
        $this->hiddenColumns[] = $header;
    }

    public function paginate(int $count): self {
        $this->perPage = $count;
        return $this;
    }

    public function build(): array {
        $paginator = $this->query->paginate($this->perPage ?? 10);

        $result = [
            'headers' => $this->columns,
            'rows'    => $paginator,
            'style'   => $this->style ?? 'full',
        ];

        if (!is_array($result)) {
            die('Table class is broken internally');
        }
        return $result;
    }
}

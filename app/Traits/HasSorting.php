<?php

namespace App\Traits;

use Livewire\Attributes\Url;

trait HasSorting
{

    #[Url(keep: true)]
    public $sortColumn = 'importance';
    #[Url(keep: true)]
    public $sortDirection = 'desc';
    
    private function isCurrentSort($column, $direction)
    {
        return $column === $this->sortColumn && $direction === $this->sortDirection;
    }

    private function getCurrentSortOption()
    {
        return $this->getSortOption($this->sortColumn);
    }

    private function getSortLabel()
    {
        $sortOption = $this->getCurrentSortOption();
        $label = __($sortOption['label']);
        $arrow = $this->sortDirection === 'asc' ? '↑' : '↓';
        return "$label $arrow";
    }

    private function getSortOption($column)
    {
        foreach (static::SORT_OPTIONS as $sortOption) {
            if ($sortOption['column'] === $column) {
                return $sortOption;
            }
        }
        throw new \RuntimeException;
    }

    public function sort($column, $direction = 'asc')
    {
        if ($this->getSortOption($column)) {
            if ($column !== $this->sortColumn) {
                $this->dispatch('sort-changed');
            }
            if ($direction !== $this->sortDirection) {
                $this->dispatch('sort-direction-changed');
            }
            $this->sortColumn = $column;
            $this->sortDirection = $direction;
        }
        $this->dispatch('bootstrap-rendered');
    }
    
}
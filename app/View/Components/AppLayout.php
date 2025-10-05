<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public $title;
    public $pageTitle;
    public $sidebarCollapsed;
    public function __construct($title = null, $pageTitle = null, $sidebarCollapsed = false) {
        $this->title = $title ?? 'Dashboard';
        $this->pageTitle = $pageTitle ?? 'Dashboard';
        $this->$sidebarCollapsed = $sidebarCollapsed;
    }
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}

<?php
namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProfileImage extends Component
{
    public $src;
    public $alt;
    public $size;
    public $uploadable;
    public $name;
    public $userId;
    /**
     * Create a new component instance.
     */
    public function __construct(
        $src = null,
        $alt = 'Profile Image',
        $size = 'md',
        $uploadable = false,
        $name = 'avatar',
        $userId = null
    ) {
        $this->src        = $src ?? $this->getDefaultAvatar();
        $this->alt        = $alt;
        $this->size       = $size;
        $this->uploadable = $uploadable;
        $this->name       = $name;
        $this->userId     = $userId;
    }

    protected function getDefaultAvatar()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->alt) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getSizeClasses()
    {
        return [
            'xs'  => 'w-6 h-6',
            'sm'  => 'w-8 h-8',
            'md'  => 'w-12 h-12',
            'lg'  => 'w-16 h-16',
            'xl'  => 'w-24 h-24',
            '2xl' => 'w-32 h-32',
        ][$this->size] ?? 'w-12 h-12';
    }

    public function getIconSize()
    {
        return [
            'xs'  => 'w-3 h-3',
            'sm'  => 'w-4 h-4',
            'md'  => 'w-5 h-5',
            'lg'  => 'w-6 h-6',
            'xl'  => 'w-8 h-8',
            '2xl' => 'w-10 h-10',
        ][$this->size] ?? 'w-5 h-5';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View | Closure | string
    {
        return view('components.profile-image');
    }
}

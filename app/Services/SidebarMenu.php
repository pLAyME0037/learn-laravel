<?php
namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

class SidebarMenu
{
    protected array $menu = [];

    /**
     * Build and return the filtered sidebar menu items.
     */
    public function getItems(Authenticatable $user): array
    {
        $this->buildMenu($user);
        return $this->filterMenuItems($this->menu);
    }

    /**
     * Defines the entire menu structure using a fluent API.
     */
    protected function buildMenu(Authenticatable $user): void
    {
        $this->addHeading('Main');
        $this->addLink('Dashboard', 'dashboard', 'heroicon-o-home');
        $this->addHeading('Admin',
            ['view.asAdmin']); // Example permission for a whole section

        $this->addDropdown(
            'Academic Records',
            'heroicon-o-academic-cap',
            function ($menu) {
                $menu->addLink(
                    'All Records',
                    'admin.academic_records.index',
                    null,
                    ['view.academic_records']
                );
                $menu->addLink(
                    'Add Record',
                    'admin.academic_records.create',
                    null,
                    ['create.academic_records']
                );
            },
            ['view.academic_records', 'create.academic_records']
        );

        $this->addLink(
            'Academic Years',
            'admin.academic-years.index',
            'heroicon-o-calendar',
            ['view.academic_years']);

        $this->addDropdown(
            'Users',
            'heroicon-o-users',
            function ($menu) {
                $menu->addLink('All Users',
                    'admin.users.index',
                    null,
                    ['view.users']
                );
                $menu->addLink('Add User',
                    'admin.users.create',
                    null,
                    ['create.users']
                );
                $menu->addLink('Manage Users',
                    'admin.users.management',
                    null,
                    ['edit-access.users']
                );
            },
            ['view.users']
        );

        // ... Add all other menu items here using the same fluent methods ...

        $this->addDropdown(
            'Roles & Permissions',
            'heroicon-o-shield-check',
            function ($menu) {
                $menu->addLink(
                    'All Roles',
                    'admin.roles.index',
                    null,
                    ['view.roles']
                );
                $menu->addLink(
                    'Create Role',
                    'admin.roles.create',
                    null,
                    ['create.roles']
                );
                $menu->addLink(
                    'Permissions',
                    'admin.permissions.index',
                    null,
                    ['view.permissions']
                );
            },
            ['view.roles', 'view.permissions']
        );
    }

    protected function addHeading(
        string $label,
        array | string | null $can = null
    ): self {
        $this->menu[] = [
            'type'  => 'heading',
            'label' => $label,
            'can'   => $can,
        ];
        return $this;
    }

    protected function addLink(
        string $label,
        string $route,
        ?string $icon = null,
        array | string | null $can = null
    ): self {
        $this->menu[] = [
            'type'  => 'link',
            'label' => $label,
            'route' => $route,
            'icon'  => $icon,
            'can'   => $can,
        ];
        return $this;
    }

    protected function addDropdown(
        string $label,
        string $icon,
        callable $childrenCallback,
        array | string | null $can = null
    ): self {
        $childrenMenu = new self();
        $childrenCallback($childrenMenu);

        $this->menu[] = [
            'type'     => 'dropdown',
            'label'    => $label,
            'icon'     => $icon,
            'can'      => $can,
            'children' => $childrenMenu->menu,
        ];
        return $this;
    }

    /**
     * Recursively filter menu items based on user permissions.
     */
    protected function filterMenuItems(array $items): array
    {
        return collect($items)->filter(function ($item) {
            // 1. Check item-level permission
            if (isset($item['can']) && ! Gate::any((array) $item['can'])) {
                return false;
            }

            // 2. If it's a dropdown, filter itschildren;
            if ($item['type'] === 'dropdown') {
                $item['children'] = $this->filterMenuItems($item['children']);
                // Hide dropdown if it has no visible children
                return count($item['children']) > 0;
            }
            return true;
        })->values()->all();
    }
}

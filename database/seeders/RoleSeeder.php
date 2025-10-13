<?php
namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'           => 'Super Administrator',
                'slug'           => 'super_admin',
                'description'    => 'Full system access with all privileges',
                'permissions'    => json_encode($this->getAllPermissions()),
                'is_system_role' => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Administrator',
                'slug'           => 'admin',
                'description'    => 'System administrator with management privileges',
                'permissions'    => json_encode($this->getAdminPermissions()),
                'is_system_role' => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Registrar',
                'slug'           => 'registrar',
                'description'    => 'Manages student records and academic operations',
                'permissions'    => json_encode($this->getRegistrarPermissions()),
                'is_system_role' => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Head of Department',
                'slug'           => 'hod',
                'description'    => 'Department head with faculty and course management',
                'permissions'    => json_encode($this->getHodPermissions()),
                'is_system_role' => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Professor',
                'slug'           => 'professor',
                'description'    => 'Teaching faculty with course management',
                'permissions'    => json_encode($this->getProfessorPermissions()),
                'is_system_role' => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Student',
                'slug'           => 'student',
                'description'    => 'Student access to academic portal',
                'permissions'    => json_encode($this->getStudentPermissions()),
                'is_system_role' => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'Parent',
                'slug'           => 'parent',
                'description'    => 'Parent access to student information',
                'permissions'    => json_encode($this->getParentPermissions()),
                'is_system_role' => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }

    private function getAllPermissions(): array
    {
        $allPermissions = [];
        foreach ($this->getAvailablePermissions() as $category => $permissions) {
            $allPermissions = array_merge($allPermissions, $permissions);
        }
        return $allPermissions;
    }

    private function getAvailablePermissions(): array
    {
        return [
            'user_management'      => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'users.impersonate',
            ],
            'role_management'      => [
                'roles.view',
                'roles.create',
                'roles.edit',
                'roles.delete',
                'roles.assign',
            ],
            'academic_management'  => [
                'departments.manage',
                'programs.manage',
                'courses.manage',
                'syllabus.manage',
            ],
            'student_management'   => [
                'students.view',
                'students.create',
                'students.edit',
                'students.delete',
                'admissions.manage',
            ],
            'financial_management' => [
                'fees.manage',
                'payments.view',
                'payments.manage',
                'scholarships.manage',
            ],
            'system_management'    => [
                'system.config',
                'backup.manage',
                'reports.view',
                'audit.view',
            ],
        ];
    }

    private function getAdminPermissions(): array
    {
        return [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.assign',
            'departments.manage', 'programs.manage', 'courses.manage',
            'students.view', 'students.create', 'students.edit',
            'fees.manage', 'payments.view', 'payments.manage',
            'system.config', 'reports.view', 'audit.view',
        ];
    }

    private function getRegistrarPermissions(): array
    {
        return [
            'students.view', 'students.create', 'students.edit',
            'programs.manage', 'courses.manage', 'departments.manage',
            'reports.view',
        ];
    }

    private function getHodPermissions(): array
    {
        return [
            'departments.manage', 'programs.manage', 'courses.manage',
            'students.view', 'reports.view',
        ];
    }

    private function getProfessorPermissions(): array
    {
        return [
            'courses.manage', 'students.view',
        ];
    }

    private function getStudentPermissions(): array
    {
        return [
            'courses.view',
        ];
    }

    private function getParentPermissions(): array
    {
        return [
            'students.view',
        ];
    }
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
        , 'slug'
        , 'description'
        , 'permission'
        , 'is_system_role'
        , 'is_active'
        ,
    ];
    protected $cast = [
        'permission' => 'array'
        , 'is_system_role' => 'boolean'
        , 'is_active' => 'boolean'
        ,
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystemRoles($query)
    {
        return $query->where('is_system_role', true);
    }
    // check if role has permission
    public function hasPermission($permission)
    {
        if ($this->permissions && in_array($permission, $this->permissions)) {
            return true;
        }
        return false;
    }

    public static function getAvailablePermission()
    {
        return [
            'user_management'      => [
                'user.view'
                , 'user.create'
                , 'user.edit'
                , 'user.delete'
                , 'user.impersonate'
                ,
            ],
            'rile_management'      => [
                'user.view'
                , 'user.create'
                , 'user.edit'
                , 'user.delete'
                , 'user.assign'
                ,
            ],
            'acadamic_management'  => [
                'department.manage'
                , 'programs.manage'
                , 'courses.manage'
                , 'syllabus.manage'
                ,
            ],
            'student_management'   => [
                'student.view'
                , 'student.create'
                , 'student.edit'
                , 'student.delete'
                , 'student.manage'
                ,
            ],
            'financial_management' => [
                'fees.manage'
                , 'payments.view'
                , 'payments.manage'
                , 'scholarships.manage'
                ,
            ],
            'system_management'    => [
                'system.config'
                , 'backup.manage'
                , 'reports.view'
                , 'audit.view'
                ,
            ],
        ];
    }
}

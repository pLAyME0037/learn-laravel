<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // Tell Laravel to use the 'sqlite_logs' connection
    protected $connection = 'sqlite_logs';

    protected $table = 'tbl_activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'expired_at',
        'description',
        'ip_address',
        'user_agent',
    ];
}

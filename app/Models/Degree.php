<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Degree extends Model {
    protected $fillable = ['name']; // e.g. "Bachelor", "Master"
}

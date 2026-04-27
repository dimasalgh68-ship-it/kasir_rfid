<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    protected $fillable = ['device_id', 'action', 'payload', 'ip_address'];
}

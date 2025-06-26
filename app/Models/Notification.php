<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'body',
        'data_id',
        'url',
        'is_sent',
        'is_seen'
    ];
}

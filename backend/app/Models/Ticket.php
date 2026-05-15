<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_uid',
        'name',
        'phone',
        'email',
        'message',
        'status',
    ];
}

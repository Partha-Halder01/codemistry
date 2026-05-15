<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatHistory extends Model
{
    protected $fillable = [
        'session_id',
        'user_message',
        'ai_response',
    ];
}

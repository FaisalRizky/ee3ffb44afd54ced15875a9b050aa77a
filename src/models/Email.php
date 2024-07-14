<?php

namespace Src\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'module', 'emailId', 'sender', 'recepient', 'subject', 'content', 'status', 'sent_at', 'remarks'
    ];
}

<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'module', 'emailId', 'recipient', 'subject', 'content', 'status', 'sent_at', 'remarks'
    ];
}

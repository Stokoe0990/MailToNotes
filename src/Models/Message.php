<?php

namespace Stokoe\MailToNotes\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'message_id',
        'context_id',
        'sender',
        'subject',
        'recipients',
        'in_reply_to',
        'references',
        'body',
    ];
}

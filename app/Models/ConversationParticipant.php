<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'last_read_at',
        'last_seen_at',
        'is_muted',
        'is_pinned',
        'is_hidden',
        'left_at',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_muted' => 'boolean',
        'is_pinned' => 'boolean',
        'is_hidden' => 'boolean',
        'left_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('tenant.{tenantId}', function ($user, $tenantId) {
    return (int) $user->tenant_id === (int) $tenantId;
});

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    
    // Pastikan user berada di tenant yang sama dan merupakan partisipan percakapan tersebut
    return (int) $user->tenant_id === (int) $conversation->tenant_id 
        && $conversation->participants()->where('user_id', $user->id)->exists();
});

Broadcast::channel('tenant.{tenantId}.presence', function ($user, $tenantId) {
    if ((int) $user->tenant_id === (int) $tenantId) {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }
    return false;
});

<?php

namespace App\Services;

use App\Events\ChatMessage\MessageDeliveried;
use App\Events\ChatMessage\MessageSent;
use App\Events\ChatMessage\MessageRead as MessageReadEvent;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageDelivery;
use App\Models\MessageRead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageChatService extends BaseService
{
    public function __construct()
    {
        //
    }

    public function makeConversationPrivate(array $data){
        return DB::transaction(function() use ($data){
            $conversation = Conversation::create([
                'tenant_id' => Auth::user()->tenant_id,
                'type' => 'private',
                'private_key' => Str::random(12),
            ]);

            $dataParticipants = [
                [
                    'conversation_id' => $conversation->id,
                    'user_id' => Auth::user()->id,
                ],
                [
                    'conversation_id' => $conversation->id,
                    'user_id' => $data['partner_id'],
                ],
            ];

            foreach($dataParticipants as $participant){
                $this->makeConversationParticipant($participant);
            }

            return $conversation;
        });
    }

    private function makeConversationParticipant(array $data){
        return DB::transaction(function() use ($data){
            $conversationParticipant = ConversationParticipant::create([
                'conversation_id' => $data['conversation_id'],
                'user_id' => $data['user_id'],
            ]);

            return $conversationParticipant;
        });
    }

    public function getConversations()
    {
        $userId = Auth::id();
        $tenantId = Auth::user()->tenant_id;
 
        return Conversation::where('tenant_id', $tenantId)
            ->whereHas('participants', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['participants.user', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function($conversation) use ($userId) {
                $partnerParticipant = $conversation->participants->first(function($p) use ($userId) {
                    return $p->user_id !== $userId;
                });
 
                $partner = $partnerParticipant ? $partnerParticipant->user : null;
 
                $myParticipant = $conversation->participants->first(function($p) use ($userId) {
                    return $p->user_id === $userId;
                });
 
                $unreadCount = 0;
                if ($myParticipant) {
                    $unreadCount = $conversation->messages()
                        ->where('user_id', '!=', $userId)
                        ->whereDoesntHave('reads', function($q) use ($userId) {
                            $q->where('user_id', $userId);
                        })
                        ->count();
                }
 
                return [
                    'id' => $conversation->id,
                    'type' => $conversation->type,
                    'name' => $conversation->type === 'private' ? ($partner ? $partner->name : 'Sistem') : $conversation->name,
                    'avatar' => $conversation->type === 'private' ? ($partner ? $partner->profile_photo_url : null) : null,
                    'partner_id' => $partner ? $partner->id : null,
                    'is_online' => $partner ? (bool)$partner->is_online : false,
                    'last_seen' => $partner ? ($partner->is_online ? '-' : ($partner->last_seen ? $partner->last_seen->diffForHumans() : '-')) : '-',
                    'last_message' => $conversation->lastMessage ? $conversation->lastMessage->body : null,
                    'last_message_time' => $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : null,
                    'unread_count' => $unreadCount,
                ];
            });
    }
 
    public function getMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        
        return $conversation->messages()
            ->with(['user', 'reads', 'deliveries'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'type' => $m->type,
                'user_id' => $m->user_id,
                'user_name' => $m->user->name,
                'time' => $m->created_at->diffForHumans(),
                'is_me' => $m->user_id === Auth::id(),
                'attachment_path' => $m->attachment_path,
                'attachment_name' => $m->attachment_name,
                'attachment_size' => $m->attachment_size,
                'attachment_mime' => $m->attachment_mime,
                'status' => $m->user_id === Auth::id()
                    ? ($m->reads->contains(fn($r) => $r->user_id !== Auth::id())
                        ? 'read'
                        : ($m->deliveries->contains(fn($d) => $d->user_id !== Auth::id())
                            ? 'delivered'
                            : 'sent'))
                    : null,
            ]);
    }
 
    public function startChatWithUser($partnerId)
    {
        $userId = Auth::id();
        $tenantId = Auth::user()->tenant_id;
 
        $conversation = Conversation::where('tenant_id', $tenantId)
            ->where('type', 'private')
            ->whereHas('participants', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('participants', function($query) use ($partnerId) {
                $query->where('user_id', $partnerId);
            })
            ->first();
 
        if (!$conversation) {
            $conversation = $this->makeConversationPrivate(['partner_id' => $partnerId]);
        }
 
        return $conversation;
    }

    public function storeMessage(array $data){
        return DB::transaction(function() use ($data){
            $message = Message::create([
                'tenant_id' => Auth::user()->tenant_id,
                'conversation_id' => $data['conversation_id'],
                'user_id' => Auth::user()->id,
                'client_id' => $data['client_id'] ?? null,
                'body' => $data['body'] ?? null,
                'type' => $data['type'] ?? 'text',
                'attachment_path' => $data['attachment_path'] ?? null,
                'attachment_name' => $data['attachment_name'] ?? null,
                'attachment_size' => $data['attachment_size'] ?? null,
                'attachment_mime' => $data['attachment_mime'] ?? null,
                'reply_to_id' => $data['reply_to_id'] ?? null,
                'forwarded_from_id' => $data['forwarded_from_id'] ?? null,
            ]);

            // Update conversation last message details
            $message->conversation()->update([
                'last_message_id' => $message->id,
                'last_message_at' => now(),
            ]);

            $message->load('user');
            event(new MessageSent($message));

            return $message;
        });
    }

    public function storeDeliveryMessage(array $data){
        return DB::transaction(function() use ($data){
            $messageDelivery = MessageDelivery::create([
                'message_id' => $data['message_id'],
                'user_id' => Auth::user()->id,
                'delivered_at' => now(),
            ]);

            $messageDelivery->load('message');
            event(new MessageDeliveried($messageDelivery->message));

            return $messageDelivery;
        });
    }

    public function storeReadMessage(array $data){
        return DB::transaction(function() use ($data){
            $messageRead = MessageRead::create([
                'message_id' => $data['message_id'],
                'user_id' => Auth::user()->id,
                'read_at' => now(),
            ]);

            $messageRead->load('message');
            event(new MessageReadEvent($messageRead->message));

            return $messageRead;
        });
    }
}
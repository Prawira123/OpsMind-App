<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\MessageChatService;
use Illuminate\Http\Request;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageChatController extends Controller
{
    public function __construct(protected MessageChatService $service)
    {
    }

    /**
     * Display the chat list interface.
     */
    public function index()
    {
        $conversations = $this->service->getConversations();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'activeConversationId' => null,
            'messages' => [],
        ]);
    }

    /**
     * Display a specific conversation room.
     */
    public function show(Conversation $conversation)
    {
        $userId = Auth::id();
        $isParticipant = $conversation->participants()->where('user_id', $userId)->exists();
        if (!$isParticipant) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        // Mark all unread messages from other users in this conversation as read
        $conversation->messages()
            ->where('user_id', '!=', $userId)
            ->get()
            ->each(function($msg) use ($userId) {
                // If there's no read record for this user, we mark it as read
                if (!$msg->reads()->where('user_id', $userId)->exists()) {
                    $this->service->storeReadMessage(['message_id' => $msg->id]);
                }
            });

        // Also update last_read_at on the participant record
        $conversation->participants()
            ->where('user_id', $userId)
            ->update(['last_read_at' => now()]);

        $conversations = $this->service->getConversations();
        $messages = $this->service->getMessages($conversation->id);

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'activeConversationId' => $conversation->id,
            'messages' => $messages,
        ]);
    }

    /**
     * Find or create a private conversation with a user, then redirect.
     */
    public function startChatWithUser($userId)
    {
        $conversation = $this->service->startChatWithUser($userId);
        return redirect()->route('chat.show', $conversation->id);
    }

    /**
     * Create a private conversation between the authenticated user and a partner.
     */
    public function makeConversationPrivate(Request $request)
    {
        $data = $request->validate([
            'partner_id' => 'required|exists:users,id',
        ]);

        $conversation = $this->service->makeConversationPrivate($data);

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
        ]);
    }

    /**
     * Store a new message in a conversation.
     */
    public function storeMessage(Request $request)
    {
        $data = $request->validate([
            'conversation_id'   => 'required|exists:conversations,id',
            'client_id'         => 'nullable|uuid',
            'body'              => 'nullable|string',
            'type'              => 'nullable|string|in:text,image,file,audio,system',
            'attachment_path'   => 'nullable|string',
            'attachment_name'   => 'nullable|string',
            'attachment_size'   => 'nullable|integer',
            'attachment_mime'   => 'nullable|string',
            'reply_to_id'       => 'nullable|exists:messages,id',
            'forwarded_from_id' => 'nullable|exists:messages,id',
        ]);

        $message = $this->service->storeMessage($data);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Mark a message as delivered.
     */
    public function storeDeliveryMessage(Request $request)
    {
        $data = $request->validate([
            'message_id' => 'required|exists:messages,id',
        ]);

        $delivery = $this->service->storeDeliveryMessage($data);

        return response()->json([
            'success' => true,
            'delivery' => $delivery,
        ]);
    }

    /**
     * Mark a message as read.
     */
    public function storeReadMessage(Request $request)
    {
        $data = $request->validate([
            'message_id' => 'required|exists:messages,id',
        ]);

        $read = $this->service->storeReadMessage($data);

        return response()->json([
            'success' => true,
            'read' => $read,
        ]);
    }
}

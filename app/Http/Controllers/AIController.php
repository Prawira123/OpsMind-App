<?php

namespace App\Http\Controllers;
 
use App\Services\AIService;
use Illuminate\Http\Request;
use Inertia\Inertia;
 
class AIController extends Controller
{
    protected $aiService;
 
    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }
 
    public function index()
    {
        return Inertia::render('ai_chatbot/index', [
            'history' => \App\Models\ChatMessage::latest()->limit(50)->get()->reverse()->values()
        ]);
    }
 
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        
        $reply = $this->aiService->chat($request->message);
        
        return response()->json([
            'reply' => $reply
        ]);
    }
}

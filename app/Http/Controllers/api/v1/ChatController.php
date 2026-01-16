<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Site;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function ask(Request $request, ChatService $chatService)
    {
        $data = $request->validate([
            'site_id' => 'required|exists:sites,id',
            'question' => 'required|string|max:1000',
            'conversation_id' => 'nullable|exists:conversations,id',
        ]);

        $site = Site::where('id', $data['site_id'])
            ->where('account_id', auth()->user()->account_id)
            ->firstOrFail();

        // 🔑 Continuité OU nouvelle conversation
        if (!empty($data['conversation_id'])) {
            $conversation = Conversation::where('id', $data['conversation_id'])
                ->where('user_id', auth()->id())
                ->firstOrFail();
        } else {
            $conversation = Conversation::create([
                'site_id' => $site->id,
                'user_id' => auth()->id(),
            ]);
        }

        // Sauvegarder la question
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'role' => 'user',
            'content' => $data['question'],
        ]);

        // Générer la réponse (🧠 avec mémoire)
        $answer = $chatService->answer(
            site: $site,
            question: $data['question'],
            conversation: $conversation
        );

        // Sauvegarder la réponse
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'role' => 'bot',
            'content' => $answer,
        ]);

        return response()->json([
            'answer' => $answer,
            'conversation_id' => $conversation->id,
        ]);
    }
}

<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Site;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'site_id' => 'required|exists:sites,id',
        ]);
        $conversations = Conversation::with('messages')
            ->where('site_id', $data['site_id'])
            ->where('user_id', auth()->id())
            ->get();

        return response()->json($conversations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //
    }

    public function messages(string $conversationId, string $siteId){
        $conversation = Conversation::where('id', $conversationId)
                        ->where('user_id', auth()->id())
                        ->where('site_id', $siteId)
                        ->with('messages')
                        ->first();

        return response()->json($conversation);
    }
}

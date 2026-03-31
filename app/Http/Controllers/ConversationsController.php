<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return $user->conversations()->with([
            'lastMessage',
            'participants' => function($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            },])
            ->withCount([
                'recipients as new_messages' => function($builder) use ($user) {
                    $builder->where('recipients.user_id', '=', $user->id)
                        ->whereNull('read_at');
                }
            ])
            ->orderByDesc('conversations.last_message_id')
            ->orderByDesc('conversations.updated_at')
            ->paginate();
    }

    public function show($id)
    {
        $user = Auth::user();
        return $user->conversations()->with([
            'lastMessage',
            'participants' => function($builder) use ($user) {
                $builder->where('id', '<>', $user->id);
            },])
            ->withCount([
                'recipients as new_messages' => function($builder) use ($user) {
                    $builder->where('recipients.user_id', '=', $user->id)
                        ->whereNull('read_at');
                }
            ])
            ->findOrFail($id);
    }

    public function addParticipant(Request $request, Conversation $conversation)
    {
        $request->validate([
            'user_id' => ['required', 'int', 'exists:users,id'],
        ]);

        abort_unless(
            $conversation->participants()->where('users.id', Auth::id())->exists(),
            403
        );

        $conversation->participants()->syncWithoutDetaching([
            $request->post('user_id') => [
                'joined_at' => Carbon::now(),
                'role' => 'member',
            ],
        ]);

        return response()->json([
            'message' => 'Participant added',
        ]);
    }

    public function removeParticipant(Request $request, Conversation $conversation)
    {
        $request->validate([
            'user_id' => ['required', 'int', 'exists:users,id'],
        ]);

        abort_unless(
            $conversation->participants()->where('users.id', Auth::id())->exists(),
            403
        );

        $conversation->participants()->detach($request->post('user_id'));

        return response()->json([
            'message' => 'Participant removed',
        ]);
    }

    public function markAsRead($id)
    {
        abort_unless(
            Auth::user()->conversations()->whereKey($id)->exists(),
            404
        );

        Recipient::where('user_id', '=', Auth::id())
            ->whereNull('read_at')
            ->whereRaw('message_id IN (
                SELECT id FROM messages WHERE conversation_id = ?
            )', [$id])
            ->update([
                'read_at' => Carbon::now(),
            ]);

        return [
            'message' => 'Messages marked as read',
        ];
    }

    public function destroy($id)
    {
        Recipient::where('user_id', '=', Auth::id())
            ->whereRaw('message_id IN (
                SELECT id FROM messages WHERE conversation_id = ?
            )', [$id])
            ->delete();

        return [
            'message' => 'Conversation deleted',
        ];
    }
}

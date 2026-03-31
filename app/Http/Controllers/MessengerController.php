<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
    public function index($id = null)
    {
        $user = Auth::user();

        $friends = User::where('id', '<>', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('messenger', [
            'friends' => $friends,
            'selectedConversationId' => $id ? (int) $id : null,
            'stats' => [
                'conversations' => $user->conversations()->count(),
                'contacts' => $friends->count(),
                'unread' => Recipient::where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->count(),
            ],
        ]);
    }
}

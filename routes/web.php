<?php

use App\Http\Controllers\MessengerController;
use App\Models\Message;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'home')->name('home');

Route::get('/dashboard', function () {
    $user = Auth::user();

    $conversationIds = $user->conversations()->pluck('conversations.id');

    return view('dashboard', [
        'stats' => [
            'conversations' => $conversationIds->count(),
            'contacts' => User::whereKeyNot($user->id)->count(),
            'unread' => Recipient::where('user_id', $user->id)
                ->whereNull('read_at')
                ->count(),
            'attachments' => Message::whereIn('conversation_id', $conversationIds)
                ->where('type', 'attachment')
                ->count(),
        ],
    ]);
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/messenger/{id?}', [MessengerController::class, 'index'])
    ->middleware('auth')
    ->name('messenger');

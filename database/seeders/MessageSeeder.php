<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('recipients')->delete();
        Message::query()->delete();

        $users = User::orderBy('id')->get()->keyBy('email');

        $omar    = $users['omar@sannad-demo.test'];
        $sara    = $users['sara@sannad-demo.test'];
        $ahmad   = $users['ahmad@sannad-demo.test'];
        $noor    = $users['noor@sannad-demo.test'];
        $lina    = $users['lina@sannad-demo.test'];
        $support = $users['support@sannad-demo.test'];
        $manager = $users['manager@sannad-demo.test'];
        $design  = $users['design@sannad-demo.test'];

        $conversations = Conversation::with('participants')->orderBy('id')->get();

        foreach ($conversations as $conversation) {
            $messages = match ($conversation->id) {
                1 => [
                    ['sender' => $sara->id, 'type' => 'text', 'body' => 'Hi Omar, I reviewed the messenger layout. It looks clean and easy to use.', 'read_by_omar' => true],
                    ['sender' => $omar->id, 'type' => 'text', 'body' => 'Great. I want the conversation list to look polished for the portfolio screenshots.', 'read_by_omar' => true],
                    ['sender' => $sara->id, 'type' => 'text', 'body' => 'Then we should keep a few active chats, show timestamps, and leave one conversation unread.', 'read_by_omar' => true],
                    ['sender' => $omar->id, 'type' => 'text', 'body' => 'Perfect. I will also make sure the sidebar has realistic names and message previews.', 'read_by_omar' => true],
                    ['sender' => $sara->id, 'type' => 'text', 'body' => 'Nice. This chat is already good for the main screenshot.', 'read_by_omar' => false],
                ],

                2 => [
                    ['sender' => $omar->id, 'type' => 'text', 'body' => 'Hey Ahmad, did you finish the real-time event testing?', 'read_by_omar' => true],
                    ['sender' => $ahmad->id, 'type' => 'text', 'body' => 'Yes, broadcasting works well. New messages appear instantly and unread counters update correctly.', 'read_by_omar' => true],
                    ['sender' => $omar->id, 'type' => 'text', 'body' => 'Great. I will use this conversation to show the technical side of the project.', 'read_by_omar' => true],
                ],

                3 => [
                    ['sender' => $support->id, 'type' => 'text', 'body' => 'Hello Omar, here is the latest product brief for the messaging module.', 'read_by_omar' => true],
                    [
                        'sender' => $support->id,
                        'type' => 'attachment',
                        'body' => [
                            'file_name' => 'messenger-product-brief.pdf',
                            'file_size' => 248000,
                            'mimetype' => 'application/pdf',
                            'file_path' => 'attachments/messenger-product-brief.pdf',
                        ],
                        'read_by_omar' => true
                    ],
                    ['sender' => $omar->id, 'type' => 'text', 'body' => 'Received. I will use this attachment in the portfolio screenshots.', 'read_by_omar' => true],
                ],

                4 => [
                    ['sender' => $manager->id, 'type' => 'text', 'body' => 'Welcome everyone to the Project Launch Team chat.', 'read_by_omar' => true],
                    ['sender' => $omar->id, 'type' => 'text', 'body' => 'Thanks. I want this group to show collaboration and multiple participants.', 'read_by_omar' => true],
                    ['sender' => $design->id, 'type' => 'text', 'body' => 'The spacing and message bubbles already look very clean.', 'read_by_omar' => true],
                    ['sender' => $noor->id, 'type' => 'text', 'body' => 'I like the unread logic and the chat ordering based on latest activity.', 'read_by_omar' => false],
                ],

                5 => [
                    ['sender' => $design->id, 'type' => 'text', 'body' => 'I updated the color balance for the UI preview.', 'read_by_omar' => true],
                    ['sender' => $lina->id, 'type' => 'text', 'body' => 'Looks better now. The sidebar and active state are much clearer.', 'read_by_omar' => true],
                    ['sender' => $sara->id, 'type' => 'text', 'body' => 'This conversation should stay near the top because it has recent activity.', 'read_by_omar' => false],
                ],

                default => [],
            };

            $start = Carbon::now()->subDays(2)->addHours($conversation->id * 2);
            $lastMessageId = null;

            foreach ($messages as $index => $data) {
                $createdAt = $start->copy()->addMinutes($index * 11);

                $message = Message::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $data['sender'],
                    'type' => $data['type'],
                    'body' => $data['body'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $recipientIds = $conversation->participants
                    ->pluck('id')
                    ->reject(fn($id) => $id === $data['sender']);

                foreach ($recipientIds as $recipientId) {
                    DB::table('recipients')->insert([
                        'user_id' => $recipientId,
                        'message_id' => $message->id,
                        'read_at' => ($recipientId === $omar->id && !($data['read_by_omar'] ?? true))
                            ? null
                            : $createdAt->copy()->addMinutes(2),
                        'deleted_at' => null,
                    ]);
                }

                $lastMessageId = $message->id;
            }

            $conversation->update([
                'last_message_id' => $lastMessageId,
                'updated_at' => optional(Message::find($lastMessageId))->created_at ?? now(),
            ]);
        }
    }
}

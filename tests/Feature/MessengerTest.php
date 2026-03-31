<?php

namespace Tests\Feature;

use App\Events\MessageCreated;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MessengerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_messenger_routes()
    {
        $this->get('/messenger')
            ->assertRedirect('/login');

        $this->get('/api/conversations')
            ->assertRedirect('/login');
    }

    public function test_authenticated_users_can_open_the_messenger_screen()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/messenger')
            ->assertOk()
            ->assertSee('Portfolio demo workspace');
    }

    public function test_authenticated_users_can_open_the_dashboard_screen()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Present the messenger with confidence');
    }

    public function test_message_requires_text_or_attachment()
    {
        [$sender, $recipient, $conversation] = $this->createConversation();

        $response = $this->actingAs($sender)->post('/api/messages', [
            'conversation_id' => $conversation->id,
        ]);

        $response->assertSessionHasErrors('message');
    }

    public function test_sender_can_create_a_message_in_an_existing_conversation()
    {
        Event::fake([MessageCreated::class]);

        [$sender, $recipient, $conversation] = $this->createConversation();

        $response = $this->actingAs($sender)->post('/api/messages', [
            'conversation_id' => $conversation->id,
            'message' => 'Hello from the test suite',
        ]);

        $response->assertCreated()
            ->assertJsonPath('body', 'Hello from the test suite');

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'user_id' => $sender->id,
        ]);

        $this->assertDatabaseHas('recipients', [
            'user_id' => $recipient->id,
        ]);
    }

    public function test_only_sender_can_delete_message_for_everyone()
    {
        [$sender, $recipient, $conversation] = $this->createConversation();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $sender->id,
            'body' => 'Top secret',
            'type' => 'text',
        ]);

        DB::table('recipients')->insert([
            'user_id' => $recipient->id,
            'message_id' => $message->id,
            'read_at' => null,
            'deleted_at' => null,
        ]);

        $this->actingAs($recipient)
            ->deleteJson('/api/messages/' . $message->id, [
                'target' => 'all',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'deleted_at' => null,
        ]);
    }

    public function test_user_cannot_start_a_conversation_with_self()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/messages', [
                'user_id' => $user->id,
                'message' => 'This should fail',
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'You cannot start a conversation with yourself.');
    }

    private function createConversation(): array
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $conversation = Conversation::create([
            'user_id' => $sender->id,
            'type' => 'peer',
        ]);

        $conversation->participants()->attach([
            $sender->id => [
                'joined_at' => now(),
                'role' => 'admin',
            ],
            $recipient->id => [
                'joined_at' => now(),
                'role' => 'member',
            ],
        ]);

        return [$sender, $recipient, $conversation];
    }
}

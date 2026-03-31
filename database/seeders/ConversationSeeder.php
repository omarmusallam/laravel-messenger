<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        Conversation::query()->delete();

        $users = User::orderBy('id')->get()->keyBy('email');

        $omar    = $users['omar@sannad-demo.test'];
        $sara    = $users['sara@sannad-demo.test'];
        $ahmad   = $users['ahmad@sannad-demo.test'];
        $noor    = $users['noor@sannad-demo.test'];
        $lina    = $users['lina@sannad-demo.test'];
        $support = $users['support@sannad-demo.test'];
        $manager = $users['manager@sannad-demo.test'];
        $design  = $users['design@sannad-demo.test'];

        // 1) Omar <-> Sara
        $c1 = Conversation::create([
            'user_id' => $omar->id,
            'type' => 'peer',
        ]);

        $c1->participants()->attach([
            $omar->id => ['joined_at' => now()->subDays(6), 'role' => 'admin'],
            $sara->id => ['joined_at' => now()->subDays(6), 'role' => 'member'],
        ]);

        // 2) Omar <-> Ahmad
        $c2 = Conversation::create([
            'user_id' => $omar->id,
            'type' => 'peer',
        ]);

        $c2->participants()->attach([
            $omar->id => ['joined_at' => now()->subDays(5), 'role' => 'admin'],
            $ahmad->id => ['joined_at' => now()->subDays(5), 'role' => 'member'],
        ]);

        // 3) Omar <-> Support Team
        $c3 = Conversation::create([
            'user_id' => $omar->id,
            'type' => 'peer',
        ]);

        $c3->participants()->attach([
            $omar->id => ['joined_at' => now()->subDays(4), 'role' => 'admin'],
            $support->id => ['joined_at' => now()->subDays(4), 'role' => 'member'],
        ]);

        // 4) Group: Project Launch Team
        $c4 = Conversation::create([
            'user_id' => $omar->id,
            'label' => 'Project Launch Team',
            'type' => 'group',
        ]);

        $c4->participants()->attach([
            $omar->id    => ['joined_at' => now()->subDays(3), 'role' => 'admin'],
            $manager->id => ['joined_at' => now()->subDays(3), 'role' => 'member'],
            $design->id  => ['joined_at' => now()->subDays(3), 'role' => 'member'],
            $noor->id    => ['joined_at' => now()->subDays(3), 'role' => 'member'],
        ]);

        // 5) Group: Design Review
        $c5 = Conversation::create([
            'user_id' => $omar->id,
            'label' => 'Design Review',
            'type' => 'group',
        ]);

        $c5->participants()->attach([
            $omar->id   => ['joined_at' => now()->subDays(2), 'role' => 'admin'],
            $lina->id   => ['joined_at' => now()->subDays(2), 'role' => 'member'],
            $design->id => ['joined_at' => now()->subDays(2), 'role' => 'member'],
            $sara->id   => ['joined_at' => now()->subDays(2), 'role' => 'member'],
        ]);
    }
}

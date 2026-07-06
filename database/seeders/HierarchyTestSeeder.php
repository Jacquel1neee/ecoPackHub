<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class HierarchyTestSeeder extends Seeder
{
    public function run(): void
    {
        // Map of emails to desired levels. Seeder will only update the `level` field
        // for existing users. It will not change role, promoted_by, path, name, or
        // any other attributes.
        $levels = [
            // Root admin should be the highest level (10)
            'admin@ecopackhub.com' => 10,
            'chuahjunjie@utar.my' => 9,
            'admin.a@ecopackhub.com' => 8,
            'member.a1@example.com' => 4,
            'member.a2@example.com' => 4,
            'admin.b@ecopackhub.com' => 6,
            'member.b1@example.com' => 6,
            'admin.c@ecopackhub.com' => 6,
            'member.c1@example.com' => 6,
            // Make legend lower than admin (not same level)
            'legend@ecopackhub.com' => 7,
        ];

        foreach ($levels as $email => $level) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->level = $level;
                $user->save();
            }
        }
    }
}

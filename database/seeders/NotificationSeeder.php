<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserNotification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 1)->first();
        if (!$admin) return;

        UserNotification::create([
            'type' => 'EnquiryUpdated',
            'notifiable_type' => get_class($admin),
            'notifiable_id' => $admin->id,
            'data' => ['title' => 'Enquiry #123 Updated', 'message' => 'Enquiry status changed to replied.'],
        ]);

        UserNotification::create([
            'type' => 'OrderShipped',
            'notifiable_type' => get_class($admin),
            'notifiable_id' => $admin->id,
            'data' => ['title' => 'Order #456 Shipped', 'message' => 'Order has been shipped via J&T.'],
        ]);
    }
}

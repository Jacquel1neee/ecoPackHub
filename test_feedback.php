<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Feedback;
$f = Feedback::create([
    'name' => 'Direct Test',
    'email' => 'direct@example.com',
    'subject' => 'Direct save',
    'message' => 'Testing direct save to feedbacks.',
    'status' => 'pending',
    'last_reply_at' => new DateTime(),
]);
echo "id={$f->id}\n";
echo "count=" . Feedback::count() . "\n";

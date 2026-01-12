<?php

/*
|--------------------------------------------------------------------------
| Test Telegram Notification
|--------------------------------------------------------------------------
|
| This file is for testing Telegram error notifications.
| Access this route to test the Telegram integration.
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/test-telegram', function () {
    $telegram = new \App\Services\TelegramService();

    // Test 1: Send a simple notification
    $result1 = $telegram->sendNotification(
        'Test Notification',
        'This is a test message from Sintiya System to verify Telegram integration is working correctly!',
        '✅'
    );

    // Test 2: Send an error notification
    $result2 = $telegram->sendErrorNotification(
        exceptionClass: 'TestException',
        message: 'This is a test error message to verify error notifications are working properly.',
        file: 'routes/test-telegram.php',
        line: 24,
        trace: "#0 routes/test-telegram.php(24): test()\n#1 [internal function]: {closure}()\n#2 ...",
        context: [
            'test_mode' => true,
            'environment' => app()->environment(),
            'timestamp' => now()->toDateTimeString(),
        ],
        url: url('/test-telegram'),
        method: 'GET',
        userId: auth()->check() ? auth()->id() : 'guest'
    );

    return response()->json([
        'message' => 'Telegram test completed',
        'simple_notification' => $result1 ? '✅ Success' : '❌ Failed',
        'error_notification' => $result2 ? '✅ Success' : '❌ Failed',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

Route::get('/test-telegram-error', function () {
    // This will trigger an exception that should be sent to Telegram
    throw new \Exception('This is a test exception to verify error notifications!');
});

Route::get('/test-telegram-queue', function () {
    $telegram = new \App\Services\TelegramService();

    // Test system health notification
    $result = $telegram->sendSystemHealth('healthy', [
        'Database Connection' => '✅ Connected',
        'Redis Connection' => '✅ Connected',
        'Queue Status' => '✅ Running',
        'Disk Space' => '✅ Available',
        'Uptime' => '99.9%',
    ]);

    return response()->json([
        'message' => 'System health test completed',
        'result' => $result ? '✅ Success' : '❌ Failed',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

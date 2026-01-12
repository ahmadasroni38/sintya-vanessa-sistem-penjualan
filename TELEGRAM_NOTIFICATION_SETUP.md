# Telegram Error Notification Setup Guide

## Overview

This project now includes a comprehensive Telegram error notification system that automatically sends detailed error reports to your Telegram bot when exceptions occur in your application.

## Features

- 🚨 **Automatic Error Notifications**: Automatically sends notifications for all exceptions
- 📊 **Rich Formatting**: HTML-formatted messages with emojis for better readability
- 🔍 **Detailed Information**: Includes exception details, file/line, URL, method, user ID, stack trace, and context
- 🎯 **Smart Filtering**: Ignores common exceptions like 404s, validation errors, and auth failures
- 🏥 **Health Checks**: Built-in system health notification feature
- 📦 **Context Data**: Includes request data and environment information
- ⏰ **Timestamps**: All messages include precise timestamps

## Setup Instructions

### 1. Add Environment Variables

Add the following to your `.env` file:

```env
# Telegram Notification Configuration
TELEGRAM_ENABLED=true
TELEGRAM_BOT_TOKEN=XXX
TELEGRAM_CHAT_ID=-XXX
TELEGRAM_MESSAGE_THREAD_ID=XXX
```

**Explanation:**
- `TELEGRAM_ENABLED`: Set to `true` to enable notifications, `false` to disable
- `TELEGRAM_BOT_TOKEN`: Your bot token (provided)
- `TELEGRAM_CHAT_ID`: The chat ID where notifications will be sent (provided)
- `TELEGRAM_MESSAGE_THREAD_ID`: Optional thread ID for organized notifications (provided)

### 2. Clear Configuration Cache

After adding environment variables, clear the config cache:

```bash
php artisan config:clear
```

## How It Works

### Automatic Error Notifications

The system automatically catches all exceptions via Laravel's exception handler in `app/Exceptions/Handler.php`. When an exception occurs:

1. The exception is caught and analyzed
2. Common exceptions (404, validation, auth) are filtered out
3. A formatted message is created with:
   - 📋 Exception class name
   - 💬 Error message
   - 🔗 Request URL
   - 📡 HTTP method
   - 📁 File path and line number
   - 👤 User ID (if authenticated)
   - ⏰ Timestamp
   - 📦 Request context data
   - 📜 First 5 lines of stack trace
4. The message is sent to your Telegram bot

### Exception Filtering

The following exceptions are **automatically skipped** to avoid spam:

- `ValidationException` - Form validation errors
- `AuthenticationException` - Login/authentication errors
- `AuthorizationException` - Permission errors
- `NotFoundHttpException` - 404 errors
- `MethodNotAllowedHttpException` - Wrong HTTP method errors
- `UnauthorizedHttpException` - Unauthorized access errors
- `MissingAbilityException` - Sanctum permission errors
- `ModelNotFoundException` - Model not found errors

You can modify the `shouldSkipNotification()` method in `app/Exceptions/Handler.php` to customize this list.

## Testing the Integration

### Test 1: Simple Notification

Visit this URL in your browser:
```
http://your-domain.com/test-telegram
```

This will send two test messages:
1. A simple notification message
2. A formatted error notification example

### Test 2: Trigger Real Exception

Visit:
```
http://your-domain.com/test-telegram-error
```

This will trigger a real exception that should be caught and sent to Telegram.

### Test 3: System Health Check

Visit:
```
http://your-domain.com/test-telegram-health
```

This sends a system health notification showing various system statuses.

**Note**: Test routes are only available when `APP_DEBUG=true` in your `.env` file.

## Example Notification Format

Here's how an error notification looks in Telegram:

```
❌ ERROR EXCEPTION

📋 Exception: Illuminate\Database\QueryException
💬 Message: SQLSTATE[42S02]: Base table or view not found...
🔗 URL: http://localhost:8000/api/users
📡 Method: GET
📁 File: app/Http/Controllers/UserController.php
📍 Line: 25
👤 User ID: 1
⏰ Time: 2026-01-12 20:04:15

📦 Context:
{
    "environment": "local",
    "request_data": {
        "page": "1",
        "limit": "10"
    }
}

📜 Stack Trace (first 5 lines):
#0 app/Http/Controllers/UserController.php(25): User::all()
#1 ...

🔔 This is an automated error notification from Sintiya System
```

## Custom Usage

### Manual Notification Sending

You can manually send notifications from anywhere in your code:

```php
use App\Services\TelegramService;

$telegram = new TelegramService();

// Send simple notification
$telegram->sendNotification(
    'Important Update',
    'Your action was completed successfully!',
    '✅'
);

// Send error notification
$telegram->sendErrorNotification(
    exceptionClass: 'CustomException',
    message: 'Something went wrong',
    file: __FILE__,
    line: __LINE__,
    trace: debug_backtrace(),
    context: ['additional_info' => 'value'],
    url: request()->fullUrl(),
    method: request()->method(),
    userId: auth()->id()
);

// Send health check
$telegram->sendSystemHealth('healthy', [
    'Database' => 'Connected',
    'Cache' => 'Connected',
]);
```

## Configuration Options

### Enable/Disable Notifications

Toggle notifications by changing the `TELEGRAM_ENABLED` environment variable:

```env
TELEGRAM_ENABLED=true   # Notifications enabled
TELEGRAM_ENABLED=false  # Notifications disabled
```

### Modify Exception Filtering

Edit `app/Exceptions/Handler.php`:

```php
protected function shouldSkipNotification(Throwable $e): bool
{
    $skipClasses = [
        \Illuminate\Validation\ValidationException::class,
        // Add or remove classes here
    ];

    foreach ($skipClasses as $class) {
        if ($e instanceof $class) {
            return true;
        }
    }

    return false;
}
```

### Customize Notification Format

Modify the `formatErrorMessage()` method in `app/Services/TelegramService.php` to change how notifications are formatted.

## Troubleshooting

### Notifications Not Sending

1. Check that `TELEGRAM_ENABLED=true` in your `.env`
2. Verify your bot token and chat ID are correct
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure your bot has permission to send messages to the chat
5. Try running `php artisan config:clear`

### Bot Cannot Access Chat

1. Make sure the bot is added to the group/channel
2. Ensure the bot has permission to send messages
3. Verify the chat ID is correct (negative for groups/channels, positive for individuals)

### Rate Limiting

If sending too many notifications, Telegram may rate-limit your bot. Consider:
- Implementing a cooldown period
- Using a queue system to throttle notifications
- Being more selective about which exceptions to notify

## Security Considerations

1. **Never commit credentials**: Your `.env` file should be in `.gitignore`
2. **Disable in production testing**: Set `TELEGRAM_ENABLED=false` when not needed
3. **Sensitive data**: The system sends request data - review what's included
4. **Remove test routes**: The test routes should be removed in production

## Files Created/Modified

### Created Files:
- `app/Services/TelegramService.php` - Main Telegram service
- `routes/test-telegram.php` - Test routes (reference only)

### Modified Files:
- `app/Exceptions/Handler.php` - Exception handler with Telegram integration
- `config/services.php` - Telegram configuration
- `.env.example` - Environment variable examples
- `routes/web.php` - Test routes for development

## Best Practices

1. **Environment-specific**: Keep `TELEGRAM_ENABLED=false` in production unless needed
2. **Monitor notifications**: Check your Telegram channel regularly for error patterns
3. **Fix issues quickly**: Address recurring errors that generate many notifications
4. **Customize filtering**: Adjust the skip list based on your needs
5. **Use health checks**: Implement periodic health checks in scheduled tasks
6. **Document issues**: Keep a record of common errors and their solutions

## Support

For issues or questions:
1. Check Laravel logs for detailed error messages
2. Verify Telegram API status
3. Review this documentation for common solutions
4. Test using the provided test routes

## Future Enhancements

Potential improvements to consider:
- Add image/screenshot attachments
- Implement notification grouping
- Add priority levels
- Create a dashboard for error tracking
- Integrate with other notification services
- Add webhook support for automation

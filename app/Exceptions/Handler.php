<?php

namespace App\Exceptions;

use App\Services\TelegramService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Check if Telegram notifications are enabled
            if (!config('services.telegram.enabled', false)) {
                return;
            }

            // Don't send notifications for these exceptions
            if ($this->shouldSkipNotification($e)) {
                return;
            }

            try {
                $telegram = new TelegramService();

                // Get request information
                $request = request();
                $url = $request?->fullUrl();
                $method = $request?->method();
                $userId = Auth::check() ? Auth::id() : null;

                // Get context data
                $context = [
                    'environment' => app()->environment(),
                    'request_data' => $request?->all(),
                ];

                // Send notification
                $telegram->sendErrorNotification(
                    exceptionClass: get_class($e),
                    message: $e->getMessage(),
                    file: $e->getFile(),
                    line: $e->getLine(),
                    trace: $e->getTraceAsString(),
                    context: $context,
                    url: $url,
                    method: $method,
                    userId: $userId
                );
            } catch (\Exception $telegramError) {
                // Log error if Telegram notification fails
                Log::error('Failed to send Telegram notification', [
                    'original_error' => $e->getMessage(),
                    'telegram_error' => $telegramError->getMessage(),
                ]);
            }
        });
    }

    /**
     * Determine if the exception should skip Telegram notification
     */
    protected function shouldSkipNotification(Throwable $e): bool
    {
        $skipClasses = [
            \Illuminate\Validation\ValidationException::class,
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
            \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException::class,
            \Laravel\Sanctum\Exceptions\MissingAbilityException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        ];

        foreach ($skipClasses as $class) {
            if ($e instanceof $class) {
                return true;
            }
        }

        return false;
    }
}

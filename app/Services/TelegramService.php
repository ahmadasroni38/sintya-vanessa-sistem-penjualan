<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;
    protected ?string $chatId;
    protected ?string $messageThreadId;
    protected ?string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
        $this->messageThreadId = config('services.telegram.message_thread_id');

        if ($this->botToken) {
            $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
        }
    }

    /**
     * Check if Telegram is properly configured
     */
    protected function isConfigured(): bool
    {
        return !empty($this->botToken) && !empty($this->chatId);
    }

    /**
     * Send message to Telegram with formatted content
     */
    public function sendMessage(string $message, bool $isError = false): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('Telegram service is not configured', [
                'bot_token' => $this->botToken ? 'set' : 'not set',
                'chat_id' => $this->chatId ? 'set' : 'not set',
            ]);
            return false;
        }

        try {
            $payload = [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ];

            if ($this->messageThreadId) {
                $payload['message_thread_id'] = $this->messageThreadId;
            }

            $response = Http::timeout(10)->post($this->apiUrl . 'sendMessage', $payload);

            if (!$response->successful()) {
                Log::error('Telegram send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram service error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Check if Telegram notifications are enabled
     */
    protected function isEnabled(): bool
    {
        return config('services.telegram.enabled', false);
    }

    /**
     * Send error notification with formatted details
     */
    public function sendErrorNotification(
        string $exceptionClass,
        string $message,
        string $file,
        int $line,
        ?string $trace = null,
        ?array $context = null,
        ?string $url = null,
        ?string $method = null,
        ?string $userId = null
    ): bool {
        // Check if Telegram notifications are enabled
        if (!$this->isEnabled()) {
            return false;
        }

        $formattedMessage = $this->formatErrorMessage(
            $exceptionClass,
            $message,
            $file,
            $line,
            $trace,
            $context,
            $url,
            $method,
            $userId
        );

        return $this->sendMessage($formattedMessage, true);
    }

    /**
     * Format error message with HTML formatting
     */
    protected function formatErrorMessage(
        string $exceptionClass,
        string $message,
        string $file,
        int $line,
        ?string $trace = null,
        ?array $context = null,
        ?string $url = null,
        ?string $method = null,
        ?string $userId = null
    ): string {
        if (str_contains($exceptionClass, 'Query') || str_contains($exceptionClass, 'SQL')) {
            $emoji = '🗄️';
        } elseif (str_contains($exceptionClass, 'Validation')) {
            $emoji = '⚠️';
        } elseif (str_contains($exceptionClass, 'Auth') || str_contains($exceptionClass, 'Unauthorized')) {
            $emoji = '🔐';
        } elseif (str_contains($exceptionClass, 'NotFound')) {
            $emoji = '🔍';
        } elseif (str_contains($exceptionClass, 'Timeout')) {
            $emoji = '⏰';
        } else {
            $emoji = '❌';
        }

        $formatted = "<b>{$emoji} ERROR EXCEPTION</b>\n\n";

        $formatted .= "<b>📋 Exception:</b> <code>{$exceptionClass}</code>\n";
        $formatted .= "<b>💬 Message:</b> " . htmlspecialchars($this->truncate($message, 500)) . "\n";

        if ($url) {
            $formatted .= "<b>🔗 URL:</b> <code>" . htmlspecialchars($this->truncate($url, 200)) . "</code>\n";
        }

        if ($method) {
            $formatted .= "<b>📡 Method:</b> <code>{$method}</code>\n";
        }

        $formatted .= "<b>📁 File:</b> <code>{$this->truncate($file, 100)}</code>\n";
        $formatted .= "<b>📍 Line:</b> <code>{$line}</code>\n";

        if ($userId) {
            $formatted .= "<b>👤 User ID:</b> <code>{$userId}</code>\n";
        }

        $formatted .= "<b>⏰ Time:</b> <code>" . now()->toDateTimeString() . "</code>\n";

        if ($context && !empty($context)) {
            $formatted .= "\n<b>📦 Context:</b>\n";
            $formatted .= "<pre>" . htmlspecialchars(json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) . "</pre>\n";
        }

        if ($trace) {
            $formatted .= "\n<b>📜 Stack Trace (first 5 lines):</b>\n";
            $traces = explode("\n", $trace);
            foreach (array_slice($traces, 0, 5) as $t) {
                $formatted .= "<code>" . htmlspecialchars($this->truncate($t, 200)) . "</code>\n";
            }
        }

        $formatted .= "\n<i>🔔 This is an automated error notification from Sintiya System</i>";

        return $formatted;
    }

    /**
     * Truncate text to specified length
     */
    protected function truncate(string $text, int $length = 100): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }

    /**
     * Send a simple notification message
     */
    public function sendNotification(string $title, string $message, string $emoji = '📢'): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $formatted = "<b>{$emoji} {$title}</b>\n\n";
        $formatted .= "<i>" . htmlspecialchars($message) . "</i>";
        $formatted .= "\n\n⏰ <i>" . now()->toDateTimeString() . "</i>";

        return $this->sendMessage($formatted);
    }

    /**
     * Send system health check notification
     */
    public function sendSystemHealth(string $status, array $details = []): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $emoji = $status === 'healthy' ? '✅' : '⚠️';
        $formatted = "<b>{$emoji} System Health Check</b>\n\n";
        $formatted .= "<b>Status:</b> <code>{$status}</code>\n";
        $formatted .= "<b>Time:</b> <code>" . now()->toDateTimeString() . "</code>\n";

        if (!empty($details)) {
            $formatted .= "\n<b>📊 Details:</b>\n";
            foreach ($details as $key => $value) {
                $formatted .= "<b>• {$key}:</b> <code>{$value}</code>\n";
            }
        }

        return $this->sendMessage($formatted);
    }
}

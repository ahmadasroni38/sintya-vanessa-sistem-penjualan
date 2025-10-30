<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportFailed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $errorMessage;
    protected $format;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $errorMessage, string $format)
    {
        $this->errorMessage = $errorMessage;
        $this->format = $format;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'Export Failed',
            'message' => "Your {$this->format} export failed: {$this->errorMessage}",
            'error_message' => $this->errorMessage,
            'format' => $this->format,
            'action_url' => route('chart-of-accounts.index'),
            'action_text' => 'Try Again',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Chart of Accounts Export Failed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Unfortunately, your {$this->format} export failed.")
            ->line("Error: {$this->errorMessage}")
            ->line("Please check your filters and try again.")
            ->line("If the problem persists, please contact our support team.")
            ->action('Try Again', route('chart-of-accounts.index'))
            ->line('You can also try exporting with different filters or a smaller date range.');
    }
}

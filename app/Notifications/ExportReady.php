<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportReady extends Notification implements ShouldQueue
{
    use Queueable;

    protected $filename;
    protected $format;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $filename, string $format)
    {
        $this->filename = $filename;
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
            'title' => 'Export Ready',
            'message' => "Your {$this->format} export is ready for download.",
            'filename' => $this->filename,
            'format' => $this->format,
            'action_url' => route('exports.download', $this->filename),
            'action_text' => 'Download Export',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): \Illuminate\Notifications\Messages\MailMessage
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Chart of Accounts Export Ready')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your {$this->format} export of chart of accounts is ready for download.")
            ->line("Filename: {$this->filename}")
            ->action('Download Export', route('exports.download', $this->filename))
            ->line('This link will expire in 24 hours.')
            ->line('If you have any questions, please contact support.');
    }
}

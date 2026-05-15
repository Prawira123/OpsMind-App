<?php

namespace App\Notifications;

use App\Models\TenantInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public int $invitationId,
        public string $tenantName,
        public string $role,
        public string $email
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Undangan Bergabung ke ' . $this->tenantName)
            ->greeting('Halo!')
            ->line('Anda telah diundang untuk bergabung dengan **' . $this->tenantName . '** sebagai **' . $this->role . '**.')
            ->line('Email yang diundang: ' . $this->email)
            ->action('Terima Undangan', route('invitations.accept', $this->invitationId))
            ->line('Tunggu email Selanjutanya jika sudah setuju untuk masuk kedalam project ini. Email tersebut berisi detail akses masuk ke dalam Dashboard')
            ->line('Terima kasih telah menggunakan OpsMind!');
    }
}

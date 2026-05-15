<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $tenantName,
        public string $name,
        public string $email,
        public string $password
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
            ->subject('Selamat Datang di ' . $this->tenantName)
            ->greeting('Selamat Datang, ' . $this->name . '!')
            ->line('Akun Anda telah berhasil diaktifkan untuk mengakses dashboard **' . $this->tenantName . '**.')
            ->line('Berikut adalah detail akses Anda:')
            ->line('**Email:** ' . $this->email)
            ->line('**Password Sementara:** ' . $this->password)
            ->action('Login Sekarang', route('login'))
            ->line('Mohon segera ganti password Anda setelah berhasil masuk demi keamanan.')
            ->line('Selamat bekerja!');
    }
}

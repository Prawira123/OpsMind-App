<?php

namespace App\Notifications;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionMail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user,
        public Subscription $subscription
    ) {
        //
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
        $plan = $this->subscription->plan;
        $isFree = floatval($plan->price) === 0;
        $billingCycle = $this->subscription->ends_at->diffInMonths($this->subscription->starts_at) >= 12 ? 'Tahunan' : 'Bulanan';

        return (new MailMessage)
            ->subject($isFree ? 'Selamat Datang di OpsMind - Paket Gratis' : 'Pembayaran Berhasil - OpsMind')
            ->greeting($isFree ? 'Selamat Datang!' : 'Pembayaran Berhasil!')
            ->line($isFree
                ? "Anda telah berlangganan paket {$plan->name} secara gratis."
                : "Terima kasih telah berlangganan paket {$plan->name}."
            )
            ->line("Detail langganan:")
            ->line("• Paket: {$plan->name}")
            ->line("• Harga: Rp " . number_format($plan->price, 0, ',', '.') . " /{$billingCycle}")
            ->line("• Masa Aktif: " . $this->subscription->starts_at->format('d M Y') . " - " . $this->subscription->ends_at->format('d M Y'))
            ->line("• Status: " . ucfirst($this->subscription->status))
            ->action('Lihat Dashboard', url('/dashboard'))
            ->line('Jika Anda memiliki pertanyaan, silakan balas email ini.')
            ->line('Terima kasih telah menggunakan OpsMind!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'plan_name' => $this->subscription->plan->name,
            'status' => $this->subscription->status,
        ];
    }
}

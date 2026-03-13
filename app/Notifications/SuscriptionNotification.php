<?php

namespace App\Notifications;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class SuscriptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public SubscriptionPlan $plan)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable) 
    {           
        return [
            'title'   => 'Berlangganan Berhasil',
            'message' => " Halo {$notifiable->name}, Selamat Anda telah Daftar sebagai tenant dengan harga {$this->plan->name}!",
            'url'     => route('dashboard'),
            'type'    => 'free_plan',
        ];         
    }
}

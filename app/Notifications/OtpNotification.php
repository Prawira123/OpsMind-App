<?php

namespace App\Notifications;

use App\Models\OTPCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Spatie\Multitenancy\Jobs\TenantAware;


class OtpNotification extends Notification implements ShouldQueue, TenantAware
{
    use Queueable;

    public ?int $tenantId = null;

    /**
     * Create a new notification instance.
     */
    public function __construct(public OTPCode $otp)
    {
        $this->tenantId = $otp->user->tenant_id;

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
        $typeLabel = match($this->otp->type){
            'email_verification' => 'Verifikasi Email',
            'forgot_password'    => 'Reset Password',
            'two_factor'         => 'Login Dua Faktor',
            'sensitive_action'   => 'Konfirmasi Aksi',
            default              => 'Verifikasi',
        };

        return (new MailMessage)
            ->subject("Kode OTP OpsMind — {$typeLabel}")

            // Baris pembuka email
            // $notifiable->name → nama user yang menerima
            ->greeting("Halo, {$notifiable->name}!")

            // Baris penjelasan konteks
            ->line("Kamu menerima email ini karena ada permintaan **{$typeLabel}** di akun OpsMind kamu.")

            // Tampilkan kode OTP dengan jelas
            // ** ** → bold di MailMessage
            ->line("Berikut kode OTP kamu:")
            ->line("## {$this->otp->code}")

            // Informasi penting tentang OTP
            ->line("Kode ini berlaku selama **10 menit** dan hanya bisa digunakan **satu kali**.")

            // Peringatan keamanan
            ->line("Jangan bagikan kode ini kepada siapapun, termasuk tim OpsMind.")

            // Informasi jika bukan user yang request
            ->line("Jika kamu tidak merasa melakukan permintaan ini, abaikan email ini.")

            // Tanda tangan
            ->salutation("Salam, Tim OpsMind");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

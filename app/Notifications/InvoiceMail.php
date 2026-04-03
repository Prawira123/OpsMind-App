<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Invoice $data)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->data->load('client', 'items', 'tenant');
        
        $status = $this->data->status;
        $number = $this->data->number;
        $tenantName = $this->data->tenant->name ?? 'perusahaan kami';
        $clientName = $this->data->client->name ?? 'Pelanggan';
        $totalFormatted = number_format($this->data->total, 0, ',', '.');

        // Config per status
        $config = match($status) {
            'paid' => [
                'subject' => "Kwitansi Pembayaran #{$number} - {$tenantName}",
                'title'   => "Terima Kasih Atas Pembayarannya!",
                'message' => "Halo {$clientName}, kami telah menerima pembayaran Anda untuk invoice #{$number}. Terima kasih telah mempercayai layanan kami.",
                'btnLabel'=> "Lihat Bukti Pembayaran",
            ],
            'overdue' => [
                'subject' => "Peringatan Jatuh Tempo: Invoice #{$number}",
                'title'   => "Invoice Melewati Jatuh Tempo",
                'message' => "Halo {$clientName}, kami menginformasikan bahwa invoice #{$number} telah melewati tanggal jatuh tempo. Mohon segera melakukan penyelesaian pembayaran.",
                'btnLabel'=> "Bayar Sekarang",
            ],
            'cancelled' => [
                'subject' => "Pembatalan Invoice #{$number}",
                'title'   => "Invoice Dibatalkan",
                'message' => "Halo {$clientName}, invoice #{$number} telah dibatalkan oleh pihak {$tenantName}.",
                'btnLabel'=> "Lihat Detail",
            ],
            default => [ // send / draft
                'subject' => "Invoice Tagihan #{$number} - {$tenantName}",
                'title'   => "Invoice Tagihan Baru",
                'message' => "Halo {$clientName}, invoice tagihan baru Anda telah tersedia. Silakan tinjau detail tagihan di bawah ini.",
                'btnLabel'=> "Download PDF Invoice",
            ],
        };

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this->data,
            'tenant' => $this->data->tenant
        ]);

        return (new MailMessage)
            ->subject($config['subject'])
            ->greeting($config['title'])
            ->line($config['message'])
            ->line("Total Tagihan: **Rp {$totalFormatted}**")
            ->action($config['btnLabel'], route('invoices.public-download', $this->data->public_token))
            ->line('Jika Anda memiliki pertanyaan, silakan hubungi tim kami.')
            ->line('Terima kasih!')
            ->attachData($pdf->output(), "Invoice-{$number}.pdf", [
                'mime' => 'application/pdf',
            ]);
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

<?php

namespace App\Notifications;

use App\Models\WalletTransaction;
use App\Models\WalletNotification;
use App\Channels\WalletDatabaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaction;
    protected $formattedAmount;

    /**
     * Create a new notification instance.
     */
    public function __construct(WalletTransaction $transaction)
    {
        $this->transaction = $transaction;
        $this->formattedAmount = number_format(abs($transaction->amount));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WalletDatabaseChannel::class];
    }

    /**
     * Get the notification class for the database driver.
     *
     * @return string
     */
    public function databaseNotificationClass()
    {
        return WalletNotification::class;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('کیف پول: تراکنش جدید');

        switch ($this->transaction->type) {
            case 'deposit':
                $message->line('مبلغ ' . $this->formattedAmount . ' تومان به کیف پول شما واریز شد.')
                    ->line('توضیحات: ' . $this->transaction->description)
                    ->line('موجودی فعلی: ' . number_format($this->transaction->balance_after) . ' تومان')
                    ->action('مشاهده تراکنش‌ها', url('/wallet/transactions'));
                break;
            case 'withdrawal':
                $message->line('مبلغ ' . $this->formattedAmount . ' تومان از کیف پول شما برداشت شد.')
                    ->line('توضیحات: ' . $this->transaction->description)
                    ->line('موجودی فعلی: ' . number_format($this->transaction->balance_after) . ' تومان')
                    ->action('مشاهده تراکنش‌ها', url('/wallet/transactions'));
                break;
            case 'transfer':
                $message->line('یک انتقال وجه به مبلغ ' . $this->formattedAmount . ' تومان انجام شد.')
                    ->line('توضیحات: ' . $this->transaction->description)
                    ->line('موجودی فعلی: ' . number_format($this->transaction->balance_after) . ' تومان')
                    ->action('مشاهده تراکنش‌ها', url('/wallet/transactions'));
                break;
            default:
                $message->line('یک تراکنش جدید در کیف پول شما انجام شد.')
                    ->line('نوع: ' . $this->transaction->type)
                    ->line('مبلغ: ' . $this->formattedAmount . ' تومان')
                    ->line('توضیحات: ' . $this->transaction->description)
                    ->action('مشاهده تراکنش‌ها', url('/wallet/transactions'));
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $title = '';
        $icon = '';
        $color = '';
        $transactionType = '';
        
        switch ($this->transaction->type) {
            case 'deposit':
                $title = 'واریز به کیف پول';
                $icon = 'bx-plus-circle';
                $color = 'success';
                $transactionType = 'واریز';
                break;
            case 'withdrawal':
                $title = 'برداشت از کیف پول';
                $icon = 'bx-minus-circle';
                $color = 'warning';
                $transactionType = 'برداشت';
                break;
            case 'transfer':
                $title = 'انتقال وجه';
                $icon = 'bx-transfer-alt';
                $color = 'info';
                $transactionType = 'انتقال';
                break;
            default:
                $title = 'تراکنش کیف پول';
                $icon = 'bx-wallet';
                $color = 'primary';
                $transactionType = $this->transaction->type;
        }
        
        return [
            'id' => $this->transaction->id,
            'title' => $title,
            'message' => "مبلغ {$this->formattedAmount} تومان {$transactionType} شد",
            'description' => $this->transaction->description,
            'amount' => $this->transaction->amount,
            'formatted_amount' => $this->formattedAmount,
            'balance_after' => $this->transaction->balance_after,
            'type' => $this->transaction->type,
            'transaction_type' => $transactionType,
            'status' => $this->transaction->status,
            'created_at' => $this->transaction->created_at,
            'icon' => $icon,
            'color' => $color,
            'url' => '/wallet/transactions'
        ];
    }
    
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

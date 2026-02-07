<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        
        return (new MailMessage)
            ->subject('Order Confirmation - Order #' . $this->order->id)
            ->greeting('Hello ' . $this->order->shipping_name . '!')
            ->line('Thank you for your order!')
            ->line('Your order has been received and is being processed.')
            ->line('**Order Details:**')
            ->line('Order Number: #' . $this->order->id)
            ->line('Total Amount: $' . number_format($this->order->total, 2))
            ->line('Payment Method: ' . strtoupper($this->order->payment_method))
            ->line('Payment Status: ' . ucfirst($this->order->payment_status))
            ->action('View Order', $frontendUrl . '/orders/' . $this->order->id)
            ->line('We will send you another email when your order ships.')
            ->line('If you have any questions, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'total' => $this->order->total,
        ];
    }
}

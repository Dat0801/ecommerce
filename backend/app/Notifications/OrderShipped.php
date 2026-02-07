<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShipped extends Notification implements ShouldQueue
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
        
        $message = (new MailMessage)
            ->subject('Your Order Has Shipped! - Order #' . $this->order->id)
            ->greeting('Hello ' . $this->order->shipping_name . '!')
            ->line('Great news! Your order has been shipped.')
            ->line('**Order #' . $this->order->id . '**');

        if ($this->order->tracking_number) {
            $message->line('**Tracking Number:** ' . $this->order->tracking_number);
            
            if ($this->order->tracking_url) {
                $message->action('Track Your Order', $this->order->tracking_url);
            } else {
                $message->action('Track Your Order', $frontendUrl . '/orders/track/' . $this->order->tracking_number);
            }
        }

        $message->line('Your order is on its way and should arrive soon.')
            ->action('View Order Details', $frontendUrl . '/orders/' . $this->order->id)
            ->line('Thank you for shopping with us!');

        return $message;
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
            'tracking_number' => $this->order->tracking_number,
        ];
    }
}

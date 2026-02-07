<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, $oldStatus, $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
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
        $statusMessages = [
            'pending' => 'Your order is pending and will be processed soon.',
            'paid' => 'Your payment has been confirmed.',
            'processing' => 'Your order is being processed.',
            'shipped' => 'Your order has been shipped!',
            'completed' => 'Your order has been completed.',
            'cancelled' => 'Your order has been cancelled.',
        ];

        $message = (new MailMessage)
            ->subject('Order Status Update - Order #' . $this->order->id)
            ->greeting('Hello ' . $this->order->shipping_name . '!')
            ->line('Your order status has been updated.')
            ->line('**Order #' . $this->order->id . '**')
            ->line('**New Status:** ' . ucfirst($this->newStatus))
            ->line($statusMessages[$this->newStatus] ?? 'Your order status has changed.');

        if ($this->newStatus === 'shipped' && $this->order->tracking_number) {
            $message->line('**Tracking Number:** ' . $this->order->tracking_number);
            if ($this->order->tracking_url) {
                $message->action('Track Your Order', $this->order->tracking_url);
            }
        }

        $message->action('View Order', $frontendUrl . '/orders/' . $this->order->id);

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
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}

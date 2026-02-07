<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $currentStock;
    protected $threshold;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product, $currentStock, $threshold = 10)
    {
        $this->product = $product;
        $this->currentStock = $currentStock;
        $this->threshold = $threshold;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A product is running low on stock.')
            ->line('**Product:** ' . $this->product->name)
            ->line('**SKU:** ' . $this->product->sku)
            ->line('**Current Stock:** ' . $this->currentStock)
            ->line('**Threshold:** ' . $this->threshold)
            ->action('View Product', url('/admin/products/' . $this->product->id))
            ->line('Please restock this product soon.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'sku' => $this->product->sku,
            'current_stock' => $this->currentStock,
            'threshold' => $this->threshold,
            'message' => "Product '{$this->product->name}' is running low on stock ({$this->currentStock} remaining).",
        ];
    }
}

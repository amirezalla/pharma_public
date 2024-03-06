<?php

namespace Botble\Ecommerce\Jobs;

use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Mail\OrderConfirmed;
use Botble\Ecommerce\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ChangeOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = Order::query()
            ->where('id', $this->order->id)
            ->where('status', '!=', OrderStatusEnum::COMPLETED)
            ->where('is_confirmed', false)
            ->first();
        if ($order) {
            $order->update([
                'is_confirmed' => true,
                'status' => OrderStatusEnum::COMPLETED,
            ]);
            Mail::to($order->address->email)->send(new OrderConfirmed($order));
        }
    }
}

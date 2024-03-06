<?php

namespace Botble\Ecommerce\Jobs;

use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Mail\OrderConfirmed;
use Botble\Ecommerce\Mail\OrderEdited;
use Botble\Ecommerce\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EditOrderJob implements ShouldQueue
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
        Mail::to($this->order->address->email)->send(new OrderEdited($this->order));
    }
}

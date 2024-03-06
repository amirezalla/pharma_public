<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderShippingAmount extends BaseModel
{
    protected $table = 'ec_order_shippingAmount';

    protected $fillable = [
        'shippingAmount',
        'order_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withDefault();
    }
}

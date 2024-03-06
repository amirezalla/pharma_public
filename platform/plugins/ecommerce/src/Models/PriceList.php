<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PriceList extends BaseModel
{
    protected $table = 'ec_pricelist';

    protected $fillable = [
        'price',
        'final_price',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,  'product_id', 'id');
    }

    public function customers(): BelongsToMany
    {
        return $this
            ->belongsToMany(Customer::class, 'customer_id', 'id');
    }
}

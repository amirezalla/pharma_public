<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarouselProducts extends BaseModel
{
    protected $table = 'ec_carousel_products';

    protected $fillable = [
        'customer_id',
        'product_id',

    ];
}

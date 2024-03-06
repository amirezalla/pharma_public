<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaveCart extends BaseModel
{
    protected $table = 'ec_save_cart';

    protected $fillable = [
        'id',
        'user_id',
        'cart',
        'expired'
    ];
}

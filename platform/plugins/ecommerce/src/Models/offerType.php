<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class offerType extends BaseModel
{
    protected $table = 'ec_offerType';

    protected $fillable = [
        'First',
        'Second',
        'Third',
        'min_price',
        'max_price',
        'show',
        'acquistati_in_precedenza',
        'expiry_limit',
        'created_at',
        'updated_at'
    ];

}



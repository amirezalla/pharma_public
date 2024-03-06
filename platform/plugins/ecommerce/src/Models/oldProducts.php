<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class offerType extends BaseModel
{
    protected $table = 'ec_oldProducts';

    protected $fillable = [
        'client_id',
        'product',
        'scadenza'
    ];

}



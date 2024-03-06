<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regione extends BaseModel
{
    protected $table = 'cities';

    protected $fillable = [
        'name',
        'state_id',
        'status',

    ];


    public function customer(): HasMany
    {
        return $this->hasMany(Customer::class, 'region_id');
    }
}

<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offers extends BaseModel
{
    protected $table = 'ec_offers';

    public function offerDetails()
    {
        return $this->hasMany(OffersDetail::class, 'offer_id', 'id');
    }

    // Example function to get active offers
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }


}

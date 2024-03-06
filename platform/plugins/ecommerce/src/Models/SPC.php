<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SPC extends BaseModel
{
    protected $table = 'ec_spc';

    protected $fillable = [
        'code',
        'type',
        'amount',
        'min_order',
        'max_order',
        'start_date',
        'expiring_date',
        'once',
        'status'
    ];

    public function customers()
    {
        return $this->belongsToMany(
            Customer::class, // Assuming the related model is Customer
            'ec_spc_customers', // Pivot table name
            'spc_id', // Foreign key on the pivot table pointing to this model
            'customer_id' // Foreign key on the pivot table pointing to the related model
        )->withPivot('status'); // if you want to fetch status field from pivot table as well
    }

}

<?php


namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OffersDetail extends BaseModel
{
    protected $table = 'ec_offer_details';



    public function product()
    {
        return $this->belongsTo(Product::class, 'id', 'product_id');
    }

    // Example function to get the customer associated with the offer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id', 'customer_id');
    }
    
    // Add more functions as needed to handle specific offer details
}

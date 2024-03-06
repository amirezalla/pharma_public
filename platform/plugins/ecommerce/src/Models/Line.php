<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Line extends BaseModel
{
    protected $table = 'ec_lines';

    protected $fillable = [
        'nome',
        'linea',
        'categoria',
        'gruppo',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function products(): HasMany
    {
        return $this
            ->hasMany(Product::class, 'linea_id');
    }
}

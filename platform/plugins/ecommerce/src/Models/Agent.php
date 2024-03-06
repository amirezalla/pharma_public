<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends BaseModel
{
    protected $table = 'ec_agent';

    protected $fillable = [
        'codice',
        'nome',
        'cognome',
        'tipologia',
        'email',
        'cellulare',
    ];


    public function customer(): HasMany
    {
        return $this->hasMany(Customer::class, 'agent_id');
    }
}

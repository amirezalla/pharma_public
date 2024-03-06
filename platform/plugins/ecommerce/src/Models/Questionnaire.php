<?php

namespace Botble\Ecommerce\Models;


use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Questionnaire extends BaseModel
{

    protected $table = 'ec_questionnaire';

    protected $fillable = [
        'title',
        'description',
        'desc',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'date',
        'end_at' => 'date',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasManyThrough(Answer::class, Question::class);
    }

    public function scopeActive(Builder $builder)
    {
        $builder->where('is_active', 1);
    }

    public function calculateAnswerPercent($count, $decimals = 2)
    {
        return round(($count / $this->answers_count) * 100, $decimals);
    }
}

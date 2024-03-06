<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;

class QuestionOption extends BaseModel
{
    protected $fillable = [
        'question_id',
        'value',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

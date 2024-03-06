<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends BaseModel
{
    protected $table = 'ec_question';

    protected $fillable = [
        'question_text',
        'question_type',
        'questionnaire_id'
    ];
    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class,'question_id');
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class,'question_id');
    }
}

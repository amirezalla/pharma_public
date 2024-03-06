<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends BaseModel
{
    public mixed $answer_text;
    protected $table = 'ec_answers_questionary';
    protected $fillable = [
        'answer_text','question_id','customer_id','question_option_id'
    ];
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}

<?php

namespace App\Http\Requests;

use Botble\Ecommerce\Models\Question;
use Botble\Ecommerce\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth('customer')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $questionnaire = Questionnaire::query()->active()->first();
        return $questionnaire->questions()->whereDoesntHave('answers', function ($q) {
            $q->where('customer_id', auth('customer')->user()->id);
        })->get()->mapWithKeys(function ($item) {
            $rules = [
                'answers.*' => ['required', 'array:question_id,' . ($item->options->count() ? 'answer_option_id' : 'answer_text')],
                'answers.*.question_id' => ['required',Rule::exists('ec_question','id')],
            ];
            if ($item->options->count()) {
                $rules['answers.*.answer_option_id'] = ['required', Rule::exists('question_options','id')];
            } else {
                $rules['answers.*.answer_text'] = ['required', 'string'];
            }
            return $rules;
        })->put('answers', ['required', 'array'])->toArray();
    }
}

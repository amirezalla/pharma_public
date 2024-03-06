<?php

namespace Botble\Ecommerce\Http\Requests;

use BaseHelper;
use Botble\Support\Http\Requests\Request;

class EditAccountRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'phone' => 'required|' . BaseHelper::getPhoneValidationRule(),
            'dob' => 'max:20|sometimes',
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => "Telefono",
            'name'=>'Ragione sociale'
        ];
    }

    public function messages()
    {


        if ($this->has('phone')) {
            $messages['phone.size'] = 'Il Telefono deve contenere almeno 8 caratteri.';
            // other phone-specific messages...
        }

        return $messages;
    }
}

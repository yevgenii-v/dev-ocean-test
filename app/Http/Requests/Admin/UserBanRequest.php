<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserBanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:users,id']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('user'),
        ]);
    }
}

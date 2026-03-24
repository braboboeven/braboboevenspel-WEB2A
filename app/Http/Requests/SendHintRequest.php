<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SendHintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->is_docent === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:normal,bigboss'],
            'hint_id' => ['required', 'integer'],
            'broadcast' => ['sometimes', 'boolean'],
            'groep_ids' => ['sometimes', 'array'],
            'groep_ids.*' => ['integer', 'exists:groeps,id'],
        ];
    }
}

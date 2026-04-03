<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ConfiscateVerdachteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_docent;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'groep_id' => ['required', 'integer', 'exists:groeps,id'],
            'verdachte_nummer' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'groep_id.required' => 'Kies een groep.',
            'groep_id.exists' => 'De gekozen groep bestaat niet.',
            'verdachte_nummer.required' => 'Vul een verdachte-nummer in.',
            'verdachte_nummer.min' => 'Verdachte-nummer moet minimaal 1 zijn.',
        ];
    }
}

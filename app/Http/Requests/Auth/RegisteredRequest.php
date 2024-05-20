<?php

namespace App\Http\Requests\Auth;

use App\Constants;
use Illuminate\Foundation\Http\FormRequest;

class RegisteredRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'channel_id' => ['required'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:players,email,' . $this->player . ',id,channel_id,' . $this->channel_id],
            'phone' => ['string'],
            'country' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Prepare inputs for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'status' => $this->toBoolean($this->status),
        ]);
    }

    /**
     * Convert to boolean
     *
     * @param $booleable
     * @return boolean
     */
    private function toBoolean($booleable)
    {
        return filter_var($booleable, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}

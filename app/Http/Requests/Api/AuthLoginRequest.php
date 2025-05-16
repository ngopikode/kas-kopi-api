<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
{
    protected mixed $field;
    
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required_without:username|string|email|exists:users,email',
            'username' => 'required_without:email|string|alpha_dash:ascii|exists:users,username',
            'password' => 'required|string',
            'remember_me' => 'boolean',
            'otp_code' => 'nullable|numeric',
            'fcm_token' => 'nullable|string',
        ];
    }


    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'username' => 'NIK',
            'password' => 'Password',
            'remember_me' => 'Remember Me',
            'otp_code' => 'OTP Code',
            'fcm_token' => 'FCM Token',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute harus diisi',
            'string' => ':attribute harus berupa string',
            'boolean' => ':attribute harus berupa boolean',
            'email' => ':attribute harus berupa email',
            'alpha_dash' => ':attribute harus berupa alfanumerik',
            'exists' => ':attribute tidak terdaftar',
            'required_without' => ':attribute harus diisi jika :values tidak diisi',
            'alpha' => ':attribute harus berupa huruf',
            'numeric' => ':attribute harus berupa angka',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->field = filter_var($this->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $this->merge([
            'field' => $this->field
        ]);
    }
}

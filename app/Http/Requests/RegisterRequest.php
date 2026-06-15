<?php
namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                Password::min(8)->letters()->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Nama wajib diisi.',
            'username.required'   => 'Username wajib diisi.',
            'username.unique'     => 'Username sudah dipakai, coba yang lain.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, tanda - dan _.',
            'email.required'      => 'Email wajib diisi.',
            'email.unique'        => 'Email sudah terdaftar.',
            'password.required'   => 'Password wajib diisi.',
            'password.min'        => 'Password minimal 8 karakter.',
        ];
    }

    protected function failedValidation(Validator $validator)
{
    // Hanya throw JSON kalau memang request dari API
    if ($this->expectsJson() || $this->is('api/*')) {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Data yang dikirim tidak valid.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }

    // Untuk form Blade biasa, pakai default Laravel (redirect back + errors)
    parent::failedValidation($validator);
}
}

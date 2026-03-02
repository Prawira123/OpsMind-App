<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Nama lengkap wajib diisi',
            'name.max'               => 'Nama maksimal 255 karakter',

            'business_name.required' => 'Nama bisnis wajib diisi',
            'business_name.max'      => 'Nama bisnis maksimal 255 karakter',

            'email.required'         => 'Email wajib diisi',
            'email.email'            => 'Format email tidak valid',
            'email.unique'           => 'Email ini sudah terdaftar',
            'email.lowercase'        => 'Email harus menggunakan huruf kecil',

            'password.required'      => 'Password wajib diisi',
            'password.confirmed'     => 'Konfirmasi password tidak cocok',
        ];
    }
}

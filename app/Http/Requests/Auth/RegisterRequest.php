<?php

namespace App\Http\Requests\Auth;

use App\Models\People;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

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
     * Espejo server-side de la validación del wizard en
     * resources/js/Pages/Auth/Register.tsx (validatePersonal / validateCredentials):
     * mismos formatos, mismos mensajes en español. El cliente valida al instante;
     * este request es la verdad final.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'second_name' => 'string|max:255',
            'middle_name' => 'required|string|max:255',
            'last_name' => 'string|max:255',
            'id_prefix' => 'required|string|max:1|in:V,E',
            'id_number' => ['required', 'string', 'regex:/^[0-9]{1,9}$/', 'unique:' . People::class],
            'address' => 'required|string|min:10',
            'birth_date' => 'required|date_format:d/m/Y|before:' . now()->subYears(18)->format('d/m/Y'),
            'gender' => 'required|string|max:1|in:M,F',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'phone_number' => ['required', 'string', 'digits_between:7,11', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/'],
            'terms_of_service' => 'required|accepted',
            'cv' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    /**
     * Custom messages in Spanish (the app ships no lang/ files, so Laravel
     * would otherwise return raw translation keys such as "validation.required").
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'email' => 'El campo :attribute debe ser un correo electrónico válido.',
            'lowercase' => 'El campo :attribute debe estar en minúsculas.',
            'in' => 'El campo :attribute tiene un valor no válido.',
            'regex' => 'El campo :attribute tiene un formato no válido.',
            'digits_between' => 'El campo :attribute debe contener entre :min y :max dígitos.',
            'date_format' => 'El campo :attribute debe tener el formato :format.',
            'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
            'confirmed' => 'Las contraseñas no coinciden.',
            'accepted' => 'Debes aceptar los términos y condiciones.',
            'unique' => 'El valor de :attribute ya está en uso.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'id_number.unique' => 'La cédula ya está registrada.',
            'phone_number.unique' => 'El teléfono ya está registrado.',
            'id_number.regex' => 'La cédula debe contener de 1 a 9 dígitos.',
            'password.regex' => 'La contraseña debe tener al menos una mayúscula, una minúscula y un número.',
            'cv.file' => 'El archivo del CV no es válido.',
            'cv.mimes' => 'El CV debe ser un archivo PDF, JPG o PNG.',
            'cv.max' => 'El CV no debe superar :max KB.',
        ];
    }

    /**
     * Human-readable attribute names used by the messages above.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'primer nombre',
            'second_name' => 'segundo nombre',
            'middle_name' => 'primer apellido',
            'last_name' => 'segundo apellido',
            'id_prefix' => 'nacionalidad',
            'id_number' => 'cédula',
            'address' => 'dirección',
            'birth_date' => 'fecha de nacimiento',
            'gender' => 'género',
            'email' => 'correo electrónico',
            'phone_number' => 'teléfono',
            'password' => 'contraseña',
            'terms_of_service' => 'términos y condiciones',
        ];
    }
}

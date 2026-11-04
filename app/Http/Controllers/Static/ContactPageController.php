<?php

namespace App\Http\Controllers\Static;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\InstitutionalSettingsService;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactPageController extends Controller
{
    public function index(InstitutionalSettingsService $institutionalSettings)
    {
        $institution = $institutionalSettings->institution();

        return Inertia::render('Contact', [
            'institution' => $institution,
        ]);
    }

    public function send(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:50|max:1000',
        ], [
            'name.required' => 'El nombre es requerido',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser una dirección de correo válida',
            'subject.required' => 'El asunto es requerido',
            'message.required' => 'El mensaje es requerido',
            'message.min' => 'El mensaje debe tener al menos 50 caracteres',
            'message.max' => 'El mensaje no debe exceder los 1000 caracteres',
        ]);

        try {
            Mail::to(config('mail.from.address'))->send(new ContactFormMail($validatedData));

            return redirect()->route('contact')->with('flash', [
                'success' => '¡Gracias por contactarnos! Hemos recibido tu mensaje y nos pondremos en contacto contigo lo más pronto posible'
            ]);
        } catch (\Throwable $th) {
            return redirect()->route('contact')->with('flash', [
                'error' => 'Hubo un error al enviar tu mensaje. Por favor, inténtalo de nuevo más tarde.'
            ]);
        }
    }
}

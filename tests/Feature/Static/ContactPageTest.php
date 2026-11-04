<?php

use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

test('Contact page is accessible', function () {
    $response = $this->get(route('contact'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Contact')
                ->has('institution')
        );

    $response->assertStatus(200);
});

test('Contact page validates form inputs strictly', function (array $payload, array $errors) {
    $response = $this->post(route('contact.send'), $payload);

    $response->assertSessionHasErrors($errors);
})->with([
    'empty payload' => [
        [],
        [
            'name' => 'El nombre es requerido',
            'email' => 'El correo electrónico es requerido',
            'subject' => 'El asunto es requerido',
            'message' => 'El mensaje es requerido',
        ],
    ],
    'invalid email' => [
        [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'subject' => 'Test Subject',
            'message' => 'Hello, this is a test message',
        ],
        [
            'email' => 'El correo electrónico debe ser una dirección de correo válida',
        ],
    ],
    'message too long' => [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => str_repeat('A', 1001),
        ],
        [
            'message' => 'El mensaje no debe exceder los 1000 caracteres',
        ],
    ],
    'message too short' => [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'Hi',
        ],
        [
            'message' => 'El mensaje debe tener al menos 50 caracteres',
        ],
    ],
]);

test('Contact page sends email successfully', function () {
    Mail::fake();

    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message that works only if it has at least 50 characters.',
    ];

    $response = $this->post(route('contact.send'), $payload)
        ->assertRedirect()
        ->assertSessionHas('flash.success', '¡Gracias por contactarnos! Hemos recibido tu mensaje y nos pondremos en contacto contigo lo más pronto posible');

    $response->assertSessionHasNoErrors();

    Mail::assertSent(ContactFormMail::class, function ($mail) use ($payload) {
        return $mail->hasTo(config('mail.from.address')) &&
            $mail->contactData['email'] === $payload['email'];
    });
});

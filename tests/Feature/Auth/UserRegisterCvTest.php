<?php

// TODO: Verificar manualmente antes de commitear y pushear.

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');

    $this->noCvData = [
        'first_name' => 'Sergio',
        'second_name' => 'Manuel',
        'middle_name' => 'Gutierrez',
        'last_name' => 'Lopez',
        'id_prefix' => 'V',
        'id_number' => '28273564',
        'address' => 'Barinas, Venezuela',
        'birth_date' => '06/11/2001',
        'gender' => 'M',
        'email' => 'sergio@example.com',
        'phone_number' => '04245412882',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms_of_service' => true,
        'cv' => null,
    ];

    $this->withCvData = [
        'first_name' => 'Sergio',
        'second_name' => 'Manuel',
        'middle_name' => 'Gutierrez',
        'last_name' => 'Lopez',
        'id_prefix' => 'V',
        'id_number' => '28273564',
        'address' => 'Barinas, Venezuela',
        'birth_date' => '06/11/2001',
        'gender' => 'M',
        'email' => 'sergio@example.com',
        'phone_number' => '04245412882',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms_of_service' => true,
        'cv' => UploadedFile::fake()->create('cv.pdf', 1024, 'application/pdf'),
    ];
});

test('User can register without a CV', function () {
    $response = $this->post(route('register'), $this->noCvData)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'email' => 'sergio@example.com',
    ]);
});

test('Registration stores an uploaded CV in documents', function () {
    $response = $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'email' => 'sergio@example.com',
    ]);

    Storage::disk('local')->assertExists('documents/'.$this->withCvData['cv']->hashName());
});

test('Registration accepts an image CV', function () {
    $this->withCvData['cv'] = UploadedFile::fake()->create('cv.jpg', 1024, 'image/jpeg');

    $response = $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', [
        'email' => 'sergio@example.com',
    ]);

    Storage::disk('local')->assertExists('documents/'.$this->withCvData['cv']->hashName());
});

test('Registration rejects a CV with an unsupported format', function () {
    $this->withCvData['cv'] = UploadedFile::fake()->create('cv.txt', 1024, 'text/plain');

    $response = $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasErrors(['cv']);

    $this->assertDatabaseMissing('users', [
        'email' => 'sergio@example.com',
    ]);

    Storage::disk('local')->assertMissing('documents/'.$this->withCvData['cv']->hashName());
});

test('Registration rejects a CV that is too large', function () {
    $this->withCvData['cv'] = UploadedFile::fake()->create('cv.txt', 3072, 'application/pdf');

    $response = $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasErrors(['cv']);

    $this->assertDatabaseMissing('users', [
        'email' => 'sergio@example.com',
    ]);

    Storage::disk('local')->assertMissing('documents/'.$this->withCvData['cv']->hashName());
});

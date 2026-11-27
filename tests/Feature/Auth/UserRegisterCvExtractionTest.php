<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Http::preventStrayRequests();
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
        'password' => 'Password123*',
        'password_confirmation' => 'Password123*',
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
        'password' => 'Password123*',
        'password_confirmation' => 'Password123*',
        'terms_of_service' => true,
        'cv' => UploadedFile::fake()->createWithContent(
            'cv.pdf',
            "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n"
        ),
    ];
});

test('Registration does not call the AI when no API key is configured', function () {
    config(['services.gemini.api_key' => null]);
    Http::fake();

    $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    Http::assertNothingSent();
});

test('Registration extracts academic and work data from the CV', function () {
    config(['services.gemini.api_key' => 'test_api_key']);

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'status' => 'completed',
            'steps' => [
                [
                    'type' => 'model_output',
                    'content' => [
                        ['type' => 'text', 'text' => json_encode([
                            'education' => [[
                                'institution_name' => 'Universidad Nacional Experimental de los Llanos Occidentales Ezequiel Zamora',
                                'degree_name' => 'Computer Engineering',
                                'field_of_study' => 'Informatica',
                                'start_date' => '2023-10-01',
                                'end_date' => '2027-08-01',
                                'description' => null,
                            ]],
                            'work_experience' => [[
                                'company_name' => 'Script',
                                'position' => 'Full-stack Dev',
                                'start_date' => '2021-01-15',
                                'end_date' => null,
                                'is_current' => true,
                                'location' => 'Barinas',
                                'description' => null,
                            ]],
                        ])],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', ['email' => 'sergio@example.com']);
    $this->assertDatabaseHas('education_records', [
        'institution_name' => 'Universidad Nacional Experimental de los Llanos Occidentales Ezequiel Zamora',
        'degree_name' => 'Computer Engineering',
    ]);
    $this->assertDatabaseHas('work_experiences', [
        'company_name' => 'Script',
        'position' => 'Full-stack Dev',
    ]);
});

test('Rows with unparseable dates are discarded and logged', function () {
    config(['services.gemini.api_key' => 'test_api_key']);
    Log::spy();

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'status' => 'completed',
            'steps' => [
                [
                    'type' => 'model_output',
                    'content' => [
                        ['type' => 'text', 'text' => json_encode([
                            'education' => [[
                                'institution_name' => 'Universidad Central de Venezuela',
                                'degree_name' => 'Computer Engineering',
                                'field_of_study' => 'Informatica',
                                'start_date' => 'June 2021',
                                'end_date' => '2023-06-01',
                                'description' => null,
                            ]],
                            'work_experience' => [[
                                'company_name' => 'Script',
                                'position' => 'Full-stack Dev',
                                'start_date' => '2021-01-15',
                                'end_date' => '2023-06-01',
                                'is_current' => false,
                                'location' => 'Barinas',
                                'description' => null,
                            ]],
                        ])],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('education_records', 0);
    $this->assertDatabaseCount('work_experiences', 1);

    Log::shouldHaveReceived('warning')
        ->withArgs(fn ($message, $context = []) => $message === 'CV extraction row discarded'
            && ($context['reason'] ?? null) === 'invalid_date')
        ->once();
});

test('Registration succeeds when the AI extraction fails', function () {
    config(['services.gemini.api_key' => 'test_api_key']);

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([], 500),
    ]);

    $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('users', ['email' => 'sergio@example.com']);
    $this->assertDatabaseCount('education_records', 0);
    $this->assertDatabaseCount('work_experiences', 0);
});

test('Incomplete rows from the AI are not persisted', function () {
    config(['services.gemini.api_key' => 'test_api_key']);

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'status' => 'completed',
            'steps' => [
                [
                    'type' => 'model_output',
                    'content' => [
                        ['type' => 'text', 'text' => json_encode([
                            'education' => [],
                            'work_experience' => [
                                [
                                    'company_name' => '',
                                    'position' => 'Full-stack Dev',
                                    'start_date' => '2021-01-15',
                                    'end_date' => '2023-06-01',
                                    'is_current' => false,
                                    'location' => 'Caracas',
                                    'description' => null,
                                ],
                                [
                                    'company_name' => 'Inversiones Script',
                                    'position' => 'Backend Dev',
                                    'start_date' => '2024-02-01',
                                    'end_date' => '2025-11-30',
                                    'is_current' => false,
                                    'location' => 'Barinas',
                                    'description' => null,
                                ],
                            ],
                        ])],
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->post(route('register'), $this->withCvData)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('work_experiences', ['position' => 'Full-stack Dev']);
    $this->assertDatabaseHas('work_experiences', [
        'position' => 'Backend Dev',
        'company_name' => 'Inversiones Script',
    ]);
});

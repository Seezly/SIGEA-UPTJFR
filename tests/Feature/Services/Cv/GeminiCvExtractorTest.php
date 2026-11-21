<?php

use App\Services\Cv\GeminiCvExtractor;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

beforeEach(function () {
    config(['services.gemini.api_key' => 'test-key']);

    $this->docxMime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    $this->pdfMime = 'application/pdf';

    $base = tempnam(sys_get_temp_dir(), 'cv');
    unlink($base);

    $this->docxPath = $base . '.docx';
    $this->pdfPath = $base . '.pdf';

    file_put_contents(
        $this->pdfPath,
        "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n"
    );

    $phpWord = new PhpWord();

    $section = $phpWord->addSection();
    $section->addText('Universidad Central de Venezuela');

    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($this->docxPath);

    $this->geminiResponse = fn(array $payload) => Http::response([
        'status' => 'completed',
        'steps' => [
            [
                'type' => 'model_output',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => json_encode($payload),
                    ],
                ],
            ],
        ],
    ], 200);
});

afterEach(function () {
    if (isset($this->docxPath) && file_exists($this->docxPath)) {
        unlink($this->docxPath);
    }

    if (isset($this->pdfPath) && file_exists($this->pdfPath)) {
        unlink($this->pdfPath);
    }
});

test('GeminiCvExtractor sends docx content as plain text and returns the parsed data', function () {
    Http::fake([
        'https://generativelanguage.googleapis.com/*' => ($this->geminiResponse)([
            'education' => [
                [
                    'institution_name' => 'Universidad Central de Venezuela',
                    'degree_name' => 'Ingeniero en Sistemas',
                    'field_of_study' => 'Informatica',
                    'start_date' => '2014-09-01',
                    'end_date' => '2020-06-30',
                    'description' => null,
                ],
            ],
            'work_experience' => [],
        ]),
    ]);

    $result = app(GeminiCvExtractor::class)->extract($this->docxPath, $this->docxMime);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/interactions'
            && $request->hasHeader('x-goog-api-key')
            && is_string($request['input'])
            && str_contains(
                $request['input'],
                'Universidad Central de Venezuela'
            );
    });

    expect($result['education'])->toHaveCount(1)
        ->and($result['education'][0]['institution_name'])->toBe('Universidad Central de Venezuela')
        ->and($result['work_experience'])->toBeEmpty();
});

test('GeminiCvExtractor sends a supported file type and returns the parsed data', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => ($this->geminiResponse)([
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
        ]),
    ]);

    $result = app(GeminiCvExtractor::class)->extract($this->pdfPath, $this->pdfMime);

    Http::assertSent(function ($request) {
        $input = $request['input'];

        return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/interactions'
            && $request->method() === 'POST'
            && is_array($input)
            && $input[0]['type'] === 'text'
            && $input[1]['type'] === 'document'
            && $input[1]['mime_type'] === 'application/pdf';
    });

    expect($result['education'][0]['degree_name'])->toBe('Computer Engineering')
        ->and($result['work_experience'][0]['position'])->toBe('Full-stack Dev')
        ->and($result['work_experience'][0]['company_name'])->toBe('Script');
});

test('GeminiCvExtractor returns empty data when the API responds with an error', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([], 400),
    ]);

    $result = app(GeminiCvExtractor::class)->extract($this->docxPath, $this->docxMime);

    expect($result)->toBe(['education' => [], 'work_experience' => []]);
});

test('GeminiCvExtractor returns empty data when the response is not valid JSON', function () {
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                ['content' => ['parts' => [['text' => 'This is not JSON text response']]]],
            ],
        ], 200),
    ]);

    $result = app(GeminiCvExtractor::class)->extract($this->docxPath, $this->docxMime);

    expect($result)->toBe(['education' => [], 'work_experience' => []]);
});

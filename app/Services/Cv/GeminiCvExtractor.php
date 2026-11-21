<?php

namespace App\Services\Cv;

use Illuminate\Support\Facades\Http;
use App\Services\Cv\DocxToText;
use InvalidArgumentException;
use RuntimeException;

/**
 * Extracts information from CVs using the Gemini API.
 *
 * @package App\Services\Cv
 */
class GeminiCvExtractor
{
    public function __construct(private DocxToText $docxToText, private readonly string $apiKey) {}

    /**
     * Extracts information from a CV file.
     *
     * @param string $filePath The path to the CV file.
     * @param string $mimeType The MIME type of the CV file.
     * @return array The extracted information.
     */
    public function extract(string $filePath, string $mimeType): array
    {
        $supportedMimeTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
        ];

        if ($mimeType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $text = $this->docxToText->convert($filePath);

            return $this->extractFromText($text);
        } elseif (in_array($mimeType, $supportedMimeTypes, true)) {
            return $this->extractFromFile($filePath);
        } else {
            throw new \InvalidArgumentException("Unsupported MIME type: $mimeType");
        }
    }

    /**
     * Extracts information from text using the Gemini API.
     *
     * @param string $text The text to extract information from.
     * @return array The extracted information.
     */
    private function extractFromText(string $text): array
    {
        $response = Http::withHeaders([
            'x-goog-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Api-Revision' => '2026-05-20'
        ])->post('https://generativelanguage.googleapis.com/v1beta/interactions', [
            'model' => 'gemini-3.5-flash-lite',
            'input' => 'Extrae la información del siguiente curriculum en formato JSON: ' . $text,
            'response_format' => $this->getResponseFormat(),
        ]);

        try {
            $response->throw();

            $body = $response->json();

            $output = collect($body['steps'] ?? [])
                ->where('type', 'model_output')
                ->flatMap(fn($step) => $step['content'] ?? [])
                ->firstWhere('type', 'text');

            if (!is_array($output) || !isset($output['text'])) {
                throw new RuntimeException('No text output found in the response.');
            }

            return json_decode(
                $output['text'],
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\Exception $e) {
            return ['education' => [], 'work_experience' => []];
        }
    }

    /**
     * Extracts information from a file.
     *
     * @param string $filePath The path to the file.
     * @return array The extracted information.
     */
    private function extractFromFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File does not exist: $filePath");
        }

        $mimeType = mime_content_type($filePath);

        if ($mimeType !== 'application/pdf' && $mimeType !== 'image/jpeg' && $mimeType !== 'image/png') {
            throw new InvalidArgumentException("Unsupported MIME type: $mimeType");
        }

        $type = match ($mimeType) {
            'application/pdf' => 'document',
            'image/jpeg', 'image/png', 'image/webp' => 'image',
            default => throw new InvalidArgumentException('Unsupported file type.'),
        };

        $input = [
            [
                'type' => 'text',
                'text' => 'Extrae la información del siguiente curriculum en formato JSON.',
            ],
            [
                'type' => $type,
                'data' => base64_encode(file_get_contents($filePath)),
                'mime_type' => $mimeType,
            ],
        ];

        $response = Http::withHeaders([
            'x-goog-api-key' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Api-Revision' => '2026-05-20',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/interactions',
            [
                'model' => 'gemini-3.5-flash-lite',
                'input' => $input,
                'response_format' => $this->getResponseFormat(),
            ]
        );

        try {
            $response->throw();

            $body = $response->json();

            $output = collect($body['steps'] ?? [])
                ->where('type', 'model_output')
                ->flatMap(fn($step) => $step['content'] ?? [])
                ->firstWhere('type', 'text');

            if (!is_array($output) || !isset($output['text'])) {
                throw new RuntimeException('No text output found in the response.');
            }

            return json_decode(
                $output['text'],
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\Exception $e) {
            return ['education' => [], 'work_experience' => []];
        }
    }

    /**
     * Returns the response format for the Gemini API.
     */
    private function getResponseFormat(): array
    {
        return [
            'type' => 'text',
            'mime_type' => 'application/json',
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'education' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'degree_name' => ['type' => 'string'],
                                'field_of_study' => ['type' => 'string'],
                                'start_date' => ['type' => 'string'],
                                'end_date' => ['type' => 'string'],
                                'description' => ['type' => 'string', 'nullable' => true],
                            ]
                        ]
                    ],
                    'work_experience' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'company_name' => ['type' => 'string'],
                                'position' => ['type' => 'string'],
                                'start_date' => ['type' => 'string'],
                                'end_date' => ['type' => 'string', 'nullable' => true],
                                'is_current' => ['type' => 'boolean'],
                                'location' => ['type' => 'string'],
                                'description' => ['type' => 'string', 'nullable' => true],
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}

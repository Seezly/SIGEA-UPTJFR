<?php

use App\Services\Cv\CvExtractionService;

test('CvExtractionService normalizeDate accepts supported date formats', function (mixed $value, ?string $expected) {
    expect((new CvExtractionService)->normalizeDate($value))->toBe($expected);
})->with([
    'ISO date' => ['2023-10-01', '2023-10-01'],
    'ISO date with surrounding spaces' => ['  2023-10-01  ', '2023-10-01'],
    'ISO datetime' => ['2021-06-01T00:00:00', '2021-06-01'],
    'ISO datetime with space separator' => ['2021-06-01 00:00:00', '2021-06-01'],
    'ISO datetime with timezone' => ['2021-06-01T00:00:00Z', '2021-06-01'],
    'non padded date' => ['2021-1-5', '2021-01-05'],
    'slash date' => ['01/10/2023', '2023-10-01'],
    'slash date with non padded parts' => ['1/10/2023', '2023-10-01'],
    'year and month' => ['2023-10', '2023-10-01'],
    'year and month with non padded month' => ['2021-6', '2021-06-01'],
    'year and month with spaces' => ['  2021-06  ', '2021-06-01'],
    'year only' => ['2023', '2023-01-01'],
    'integer year' => [2021, '2021-01-01'],
]);

test('CvExtractionService normalizeDate rejects impossible dates', function (mixed $value, ?string $expected) {
    expect((new CvExtractionService)->normalizeDate($value))->toBe($expected);
})->with([
    'impossible day' => ['2023-02-30', null],
    'impossible month' => ['2023-13-01', null],
    'impossible slash date' => ['31/04/2021', null],
]);

test('CvExtractionService normalizeDate rejects unsupported formats', function (mixed $value, ?string $expected) {
    expect((new CvExtractionService)->normalizeDate($value))->toBe($expected);
})->with([
    'month name' => ['June 2021', null],
    'null' => [null, null],
    'empty string' => ['', null],
    'array value' => [[], null],
]);

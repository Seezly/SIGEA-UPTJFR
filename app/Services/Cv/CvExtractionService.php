<?php

namespace App\Services\Cv;

use App\Models\ApplicantProfile;
use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Extracts CV data with the AI extractor and persists the resulting records.
 *
 * @package App\Services\Cv
 */
class CvExtractionService
{
    /**
     * Extract data from the uploaded CV and persist the resulting records.
     *
     * Registration must never fail because of the AI extraction, so any
     * error is logged and swallowed. Does nothing when there is no document
     * or no API key configured.
     */
    public function extractAndPersist(?Document $document, ApplicantProfile $profile): void
    {
        if ($document === null || ! config('services.gemini.api_key')) {
            return;
        }

        try {
            $data = app(GeminiCvExtractor::class)->extract(
                Storage::disk('local')->path($document->file_path),
                $document->mime_type,
            );

            $this->persist($data, $profile);
        } catch (Throwable $exception) {
            Log::warning('CV extraction failed', [
                'document_id' => $document->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Persist the extracted education and work records, skipping incomplete
     * rows or rows whose dates cannot be normalized. Skipped rows are logged
     * for diagnosis.
     */
    private function persist(array $data, ApplicantProfile $profile): void
    {
        $education = is_array($data['education'] ?? null) ? $data['education'] : [];

        foreach ($education as $record) {
            if (! is_array($record)) {
                $this->discardRow($profile, 'education', 'invalid_row', $record);
                continue;
            }

            if (blank($record['institution_name'] ?? null) || blank($record['degree_name'] ?? null)) {
                $this->discardRow($profile, 'education', 'missing_required_field', $record);
                continue;
            }

            $startDate = $this->normalizeDate($record['start_date'] ?? null);
            $endDate = $this->normalizeDate($record['end_date'] ?? null);

            if ($startDate === null || $endDate === null) {
                $this->discardRow($profile, 'education', 'invalid_date', $record);
                continue;
            }

            $profile->educationRecords()->create([
                'institution_name' => $record['institution_name'],
                'degree_name' => $record['degree_name'],
                'field_of_study' => $record['field_of_study'] ?? '',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'description' => $record['description'] ?? null,
            ]);
        }

        $experiences = is_array($data['work_experience'] ?? null) ? $data['work_experience'] : [];

        foreach ($experiences as $record) {
            if (!is_array($record)) {
                $this->discardRow($profile, 'work_experience', 'invalid_row', $record);
                continue;
            }

            $isCurrent = filter_var($record['is_current'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $startDate = $this->normalizeDate($record['start_date'] ?? null);
            $endDate = $isCurrent
                ? today()->toDateString()
                : $this->normalizeDate($record['end_date'] ?? null);

            if (blank($record['company_name'] ?? null) || blank($record['position'] ?? null)) {
                $this->discardRow($profile, 'work_experience', 'missing_required_field', $record);
                continue;
            }

            if ($startDate === null || $endDate === null) {
                $this->discardRow($profile, 'work_experience', 'invalid_date', $record);
                continue;
            }

            $profile->workExperiences()->create([
                'company_name' => $record['company_name'],
                'position' => $record['position'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_current' => $isCurrent,
                'location' => $record['location'] ?? '',
                'description' => $record['description'] ?? null,
            ]);
        }
    }

    /**
     * Log a row that was discarded during persistence.
     */
    private function discardRow(ApplicantProfile $profile, string $type, string $reason, mixed $row): void
    {
        Log::warning('CV extraction row discarded', [
            'profile_id' => $profile->id,
            'type' => $type,
            'reason' => $reason,
            'row' => $row,
        ]);
    }

    /**
     * Normalize a date string to Y-m-d.
     *
     * Accepts Y-m-d (with optional time part), Y-m, Y and d/m/Y formats,
     * with or without zero padding.
     *
     * Returns null when the value cannot be normalized.
     */
    public function normalizeDate(mixed $value): ?string
    {
        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Y-m-d, Y-n-j, optionally followed by a time part (e.g. 2021-06-01T00:00:00).
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})(?:[T\s].*)?$/', $value, $matches)) {
            return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1])
                ? sprintf('%04d-%02d-%02d', (int) $matches[1], (int) $matches[2], (int) $matches[3])
                : null;
        }

        // Y-m or Y-n.
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $value, $matches)) {
            return checkdate((int) $matches[2], 1, (int) $matches[1])
                ? sprintf('%04d-%02d-01', (int) $matches[1], (int) $matches[2])
                : null;
        }

        // Y.
        if (preg_match('/^\d{4}$/', $value)) {
            return $value . '-01-01';
        }

        // d/m/Y (e.g. 01/10/2023).
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $matches)) {
            return checkdate((int) $matches[2], (int) $matches[1], (int) $matches[3])
                ? sprintf('%04d-%02d-%02d', (int) $matches[3], (int) $matches[2], (int) $matches[1])
                : null;
        }

        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'applicant_profile_id',
        'institution_name',
        'degree_name',
        'field_of_study',
        'start_date',
        'end_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function applicantProfile(): BelongsTo
    {
        return $this->belongsTo(ApplicantProfile::class);
    }
}

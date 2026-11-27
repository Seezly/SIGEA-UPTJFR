<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantProfile extends Model
{
    use softDeletes;

    protected $fillable = [
        'people_id',
        'current_curriculum_vitae_id',
    ];

    /**
     * Get the education records associated with the applicant profile.
     */
    public function educationRecords(): HasMany
    {
        return $this->hasMany(EducationRecord::class);
    }

    /**
     * Get the work experiences associated with the applicant profile.
     */
    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class);
    }
}

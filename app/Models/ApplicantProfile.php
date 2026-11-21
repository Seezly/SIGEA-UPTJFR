<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicantProfile extends Model
{
    use softDeletes;

    protected $fillable = [
        'people_id',
        'current_curriculum_vitae_id',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionalSetting extends Model
{
    /** @use HasFactory<\Database\Factories\InstitutionalSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'value_type',
    ];
}

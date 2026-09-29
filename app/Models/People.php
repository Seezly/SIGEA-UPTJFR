<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class People extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'first_name',
        'second_name',
        'middle_name',
        'last_name',
        'address',
        'gender',
        'birth_date',
        'id_prefix',
        'id_number',
    ];

    protected function birthDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->format('d/m/Y') : null,
            set: fn($value) => $value ? Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d') : null,
        );
    }
}

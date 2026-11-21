<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class People extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * Get the user associated with the person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Get the documents associated with the person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the applicant profile associated with the person.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function applicantProfile()
    {
        return $this->hasOne(ApplicantProfile::class);
    }

    /**
     * Get the birth date of the person in dd/mm/yyyy format.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function birthDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->format('d/m/Y') : null,
            set: fn($value) => $value ? Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d') : null,
        );
    }
}

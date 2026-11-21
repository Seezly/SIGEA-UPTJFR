<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use softDeletes;

    protected $fillable = [
        'people_id',
        'document_type',
        'file_name',
        'mime_type',
        'file_path',
        'file_size',
        'verification_status',
    ];

    /**
     * Get the person associated with the document.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function people()
    {
        return $this->belongsTo(People::class);
    }
}

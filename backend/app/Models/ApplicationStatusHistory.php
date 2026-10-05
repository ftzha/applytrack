<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationStatusHistory extends Model
{
    protected $fillable = [
        'application_id',
        'from_status',
        'to_status',
    ];

    // Each history entry belongs to one application.
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}

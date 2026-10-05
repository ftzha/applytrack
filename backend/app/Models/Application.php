<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'position',
        'location',
        'employment_type',
        'work_mode',
        'salary_min',
        'salary_max',
        'currency',
        'source',
        'job_url',
        'status',
        'applied_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'applied_at' => 'date',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Complete status-change history for this application.
    public function statusHistories(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }
}

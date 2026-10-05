<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case INTERESTED = 'interested';
    case APPLIED = 'applied';
    case SCREENING = 'screening';
    case INTERVIEW = 'interview';
    case OFFER = 'offer';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::INTERESTED => 'Interested',
            self::APPLIED => 'Applied',
            self::SCREENING => 'Screening',
            self::INTERVIEW => 'Interview',
            self::OFFER => 'Offer',
            self::REJECTED => 'Rejected',
            self::WITHDRAWN => 'Withdrawn',
        };
    }
}

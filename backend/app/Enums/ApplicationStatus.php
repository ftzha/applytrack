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
}

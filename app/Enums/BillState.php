<?php

namespace App\Enums;

enum BillState: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case WAITING_FOR_GEPG = 'waiting-for-gepg';
    case FAILED = 'failed';
}


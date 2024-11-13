<?php

namespace App\Schedules;

use App\Events\ReconciliationRequested;

class ReconciliationSchedule
{
    public function __invoke()
    {
        ReconciliationRequested::dispatch(now());
    }
}

<?php

namespace Modules\Crm\Services;

use Modules\Crm\Enums\RequestStatus;

class StatusService
{

    public function listAsSelect(array $filter = [])
    {
        return RequestStatus::options();
    }
}

<?php

namespace Modules\Crm\Services;

use Modules\Crm\Enums\ContactTypes;

class ContactTypeService
{
    public function listAsSelect(array $filter = [])
    {
        return ContactTypes::options();
    }
}

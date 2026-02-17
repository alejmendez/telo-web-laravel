<?php

namespace Modules\Crm\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;

class ServicesResourceCollection extends PaginatedResourceResponse
{
    public function toArray(Request $request): array
    {
        return ['data' => $this->collection];
    }
}

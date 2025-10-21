<?php

namespace Modules\Core\Services\Contracts;

use Illuminate\Http\Request;
use Modules\Users\Models\User;

interface MenuServiceContract
{
    public function getMenu(Request $request, ?User $user = null): array;
}

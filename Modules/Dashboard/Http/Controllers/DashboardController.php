<?php

namespace Modules\Dashboard\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Dashboard\Services\ShowDashboard;

class DashboardController extends Controller
{
    public function __construct(private readonly ShowDashboard $showDashboard)
    {
    }

    public function index()
    {
        $data = $this->showDashboard->execute();
        return Inertia::render('Dashboard::Index', $data);
    }
}

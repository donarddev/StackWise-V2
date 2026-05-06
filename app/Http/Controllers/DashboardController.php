<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function index(): View
    {
        $data = $this->dashboardService->getDashboardData();

        return view('dashboard.index', [
            'statistics' => $data['statistics'] ?? [],
            'recentRecommendations' => $data['recentRecommendations'] ?? [],
        ]);
    }
}

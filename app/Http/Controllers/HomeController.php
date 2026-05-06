<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService,
    ) {
    }

    public function index(): View
    {
        return view('home.index', $this->homeService->getHomePageData());
    }
}

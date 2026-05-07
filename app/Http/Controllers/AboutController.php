<?php

namespace App\Http\Controllers;

use App\Services\AboutService;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    public function __construct(
        private readonly AboutService $aboutService,
    ) {}

    public function index(): View
    {
        return view('about.index', $this->aboutService->getAboutPageData());
    }
}

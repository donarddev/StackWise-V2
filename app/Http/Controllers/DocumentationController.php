<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentationFilterRequest;
use App\Services\DocumentationService;
use Illuminate\Contracts\View\View;

class DocumentationController extends Controller
{
    public function __construct(
        private readonly DocumentationService $documentationService,
    ) {}

    public function index(DocumentationFilterRequest $request): View
    {
        return view(
            'documentation.index',
            $this->documentationService->getFilteredExplorerData($request->validated()),
        );
    }
}

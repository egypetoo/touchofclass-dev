<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LayoutResource;
use App\Services\SiteLayoutDataService;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, SiteLayoutDataService $layout)
    {
        return LayoutResource::make([
            'siteSetting' => $layout->siteSetting(),
            'mainMenu' => $layout->mainNavigation(),
            'footerNavigation' => $layout->footerNavigation(),
            'mainBranch' => $layout->mainBranch(),
        ]);
    }
}

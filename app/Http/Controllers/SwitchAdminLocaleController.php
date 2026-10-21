<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetAdminLocale;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SwitchAdminLocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        $request->session()->put(SetAdminLocale::SESSION_KEY, $locale);

        return redirect()->back(fallback: Filament::getPanel('admin')->getUrl());
    }
}

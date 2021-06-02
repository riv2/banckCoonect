<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        $locales = array_keys(config('app.locales'));
        if (!in_array($locale, $locales)) {
            abort(404);
        }
        $cookie = cookie()->forever('locale', $locale);
        $path = $request->get('redirect', '/');

        return redirect()->intended($path)->withCookie($cookie);
    }
}

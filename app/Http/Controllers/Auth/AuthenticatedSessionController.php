<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $guestCart = session('cart', []);
        $request->session()->regenerate();

        CartService::restoreForUser($request->user()->id, $guestCart);

        // Admin: روح للداشبوررد دائمًا
        if (Auth::user()->role == '1') {
            return redirect()->route('ad.master');
        }

        // User: ارجع للرابط المقصود (مثل /checkout) أو fallback للصفحة الرئيسية
        return redirect()->intended('/');
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()) {
            CartService::clearForLogout($request->user()->id);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

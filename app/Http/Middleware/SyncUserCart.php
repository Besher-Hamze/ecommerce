<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SyncUserCart
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::id();

            if (session('cart_owner_id') !== $userId) {
                CartService::restoreForUser($userId, session('cart', []));
            } elseif (empty(session('cart', []))) {
                $dbCart = CartService::loadFromDatabase($userId);

                if (!empty($dbCart)) {
                    session(['cart' => $dbCart, 'cart_owner_id' => $userId]);
                }
            }
        } elseif (session()->has('cart_owner_id')) {
            CartService::clearSessionCart();
        }

        return $next($request);
    }
}

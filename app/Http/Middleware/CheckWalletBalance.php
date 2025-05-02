<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckWalletBalance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $service = 'email', int $count = 1): Response
    {
        $user = Auth::user();
        
        // If no authenticated user, just proceed
        if (!$user) {
            return $next($request);
        }
        
        // Check if user has wallet and enough credits
        if (!$user->hasEnoughCreditsFor($service, $count)) {
            // Handle insufficient funds - redirect to wallet deposit page
            return redirect()->route('wallet.showDepositForm')
                ->with('error', "Insufficient credits for {$service} service. Please add funds to your wallet.");
        }
        
        return $next($request);
    }
} 
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Filament\Pages\TwoFactorChallenge;
use App\Filament\Pages\FirstLoginPasswordChange;
use App\Filament\Pages\TwoFactorSetup;
use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceTwoFactorSetup
{
    public const SESSION_KEY = 'two_factor_passed';

    /**
     * Forza setup/challenge 2FA nel panel autenticato.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel();

        // 🔹 Route base auth Filament
        $loginRouteName = $panel?->generateRouteName('auth.login');
        $logoutRouteName = $panel?->generateRouteName('auth.logout');

        $setupRouteName = TwoFactorSetup::getRouteName($panel);
        $challengeRouteName = TwoFactorChallenge::getRouteName($panel);
        $changePasswordRouteName = FirstLoginPasswordChange::getRouteName($panel);

        // 🔹 Esclusioni critiche per evitare loop
        if (
            ($loginRouteName && $request->routeIs($loginRouteName)) ||
            ($logoutRouteName && $request->routeIs($logoutRouteName)) ||
            ($setupRouteName && $request->routeIs($setupRouteName)) ||
            ($challengeRouteName && $request->routeIs($challengeRouteName)) ||
            ($changePasswordRouteName && $request->routeIs($changePasswordRouteName))
        ) {
            return $next($request);
        }

        // 🔹 Se 2FA NON configurato → vai a setup
        if (! $user->hasConfiguredTwoFactor()) {
            return redirect()->to(
                TwoFactorSetup::getUrl(panel: $panel?->getId())
            );
        }

        // 🔹 Se configurato ma non validato in sessione → challenge
        if (! (bool) $request->session()->get(self::SESSION_KEY, false)) {
            return redirect()->to(
                TwoFactorChallenge::getUrl(panel: $panel?->getId())
            );
        }

        return $next($request);
    }
}

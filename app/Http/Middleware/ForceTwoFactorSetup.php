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

        $setupRouteName = TwoFactorSetup::getRouteName($panel);
        $challengeRouteName = TwoFactorChallenge::getRouteName($panel);
        $changePasswordRouteName = FirstLoginPasswordChange::getRouteName($panel);
        $logoutRouteName = $panel?->generateRouteName('auth.logout');

        // Consenti sempre il logout, anche prima del completamento 2FA.
        if (($logoutRouteName !== null) && $request->routeIs($logoutRouteName)) {
            return $next($request);
        }

        // Consenti sempre la pagina cambio password: è gestita da middleware dedicato.
        if ($request->routeIs($changePasswordRouteName)) {
            return $next($request);
        }

        if (! $user->hasConfiguredTwoFactor()) {
            if ($request->routeIs($setupRouteName)) {
                return $next($request);
            }

            return redirect()->to(TwoFactorSetup::getUrl(panel: $panel?->getId()));
        }

        if ((bool) $request->session()->get(self::SESSION_KEY, false)) {
            return $next($request);
        }

        if ($request->routeIs($challengeRouteName)) {
            return $next($request);
        }

        return redirect()->to(TwoFactorChallenge::getUrl(panel: $panel?->getId()));
    }
}

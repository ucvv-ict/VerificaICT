<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Filament\Pages\FirstLoginPasswordChange;
use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Forza il cambio password al primo accesso.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        if (! $user->mustChangePassword()) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel();
        $changePasswordRouteName = FirstLoginPasswordChange::getRouteName($panel);
        $logoutRouteName = $panel?->generateRouteName('auth.logout');

        if ($request->routeIs($changePasswordRouteName) || (($logoutRouteName !== null) && $request->routeIs($logoutRouteName))) {
            return $next($request);
        }

        return redirect()->to(FirstLoginPasswordChange::getUrl(panel: $panel?->getId()));
    }
}

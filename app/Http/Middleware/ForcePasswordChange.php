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
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel();

        $loginRoute = $panel?->generateRouteName('auth.login');
        $logoutRoute = $panel?->generateRouteName('auth.logout');
        $changePasswordRoute = FirstLoginPasswordChange::getRouteName($panel);

        // 🔹 Escludi route critiche
        if (
            ($loginRoute && $request->routeIs($loginRoute)) ||
            ($logoutRoute && $request->routeIs($logoutRoute)) ||
            ($changePasswordRoute && $request->routeIs($changePasswordRoute))
        ) {
            return $next($request);
        }

        if (is_null($user->password_changed_at)) {
            return redirect()->to(
                FirstLoginPasswordChange::getUrl(panel: $panel?->getId())
            );
        }

        return $next($request);
    }
}

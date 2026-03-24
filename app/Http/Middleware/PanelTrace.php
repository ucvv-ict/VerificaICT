<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PanelTrace
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('PANEL TRACE', [
            'url' => $request->fullUrl(),
            'user_id' => auth()->id(),
            'email' => auth()->user()?->email,
            'roles' => auth()->user()?->role ?? null,
            'session_id' => session()->getId(),
            'has_session' => session()->isStarted(),
        ]);

        return $next($request);
    }
}
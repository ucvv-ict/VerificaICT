<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CriticalSecurityTasksWidget;
use App\Filament\Widgets\SecurityOverviewWidget;
use App\Filament\Widgets\OperatorTasksWidget;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Dashboard;

class MainDashboard extends Dashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::Home;


    public function getWidgets(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        if (! $user->is_admin || request()->get('mode') === 'operator') {
            return [
                \App\Filament\Widgets\OperatorTasksWidget::class,
            ];
        }

        return [
            \App\Filament\Widgets\SecurityOverviewWidget::class,
            \App\Filament\Widgets\CriticalSecurityTasksWidget::class,
        ];
    }

    private function isOperatorMode(): bool
    {
        $user = auth()->user();

        if (! $user->is_admin) {
            return true;
        }

        return request()->get('mode') === 'operator';
    }
}
<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;

class AdminDashboard extends Page
{
    protected static string | UnitEnum | null $navigationGroup = 'Operativo';
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Dashboard Globale';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'dashboard-globale';


}

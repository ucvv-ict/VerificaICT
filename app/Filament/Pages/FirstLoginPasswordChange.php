<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class FirstLoginPasswordChange extends Page
{
    protected static ?string $slug = 'password/change';

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $heading = 'Cambia password al primo accesso';

    protected ?string $subheading = 'Per proseguire devi impostare una nuova password personale.';

    protected string $view = 'filament.pages.first-login-password-change';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();

        if (($user instanceof User) && (! $user->mustChangePassword())) {
            $this->redirect(TwoFactorSetup::getUrl(panel: Filament::getCurrentPanel()?->getId()));
        }
    }

    public function changePassword(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $this->validate([
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $user->forceFill([
            'password' => Hash::make($this->password),
            'force_password_change' => false,
        ])->save();

        $this->redirect(TwoFactorSetup::getUrl(panel: Filament::getCurrentPanel()?->getId()));
    }
}

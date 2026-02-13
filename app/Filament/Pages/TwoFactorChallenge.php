<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Http\Middleware\ForceTwoFactorSetup;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TwoFactorChallenge extends Page
{
    protected static ?string $slug = '2fa/challenge';

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $heading = 'Verifica autenticazione a due fattori';

    protected ?string $subheading = 'Inserisci il codice OTP generato dalla tua app di autenticazione.';

    protected string $view = 'filament.pages.two-factor-challenge';

    public string $otp = '';

    protected function redirectAfterVerification(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        if ($user->isAdmin()) {
            $this->redirect('/admin');
        } else {
            $this->redirect('/operatore');
        }
    }

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        if (! $user->hasConfiguredTwoFactor()) {
            $this->redirect(
                TwoFactorSetup::getUrl(panel: Filament::getCurrentPanel()?->getId())
            );

            return;
        }

        if ((bool) session()->get(ForceTwoFactorSetup::SESSION_KEY, false)) {
            $this->redirectAfterVerification();
        }
    }

    public function verifyOtp(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $secret = $user->getTwoFactorSecretDecrypted();

        if ($secret === null) {
            Notification::make()
                ->title('Secret 2FA non disponibile. Contatta un amministratore.')
                ->danger()
                ->send();

            return;
        }

        if (! (bool) Google2FA::verifyKey($secret, $this->otp, 0)) {
            $this->addError('otp', 'Codice OTP non valido o scaduto.');

            return;
        }

        session()->put(ForceTwoFactorSetup::SESSION_KEY, true);

        $this->redirectAfterVerification();
    }
}

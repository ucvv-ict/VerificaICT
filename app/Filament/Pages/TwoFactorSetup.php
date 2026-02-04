<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Http\Middleware\ForceTwoFactorSetup;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Session;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TwoFactorSetup extends Page
{
    private const SESSION_SETUP_SECRET = 'two_factor.setup_secret';

    protected static ?string $slug = '2fa/setup';

    protected static bool $shouldRegisterNavigation = false;

    protected ?string $heading = 'Configurazione autenticazione a due fattori';

    protected ?string $subheading = 'Per motivi di sicurezza devi completare il setup del 2FA prima di usare il pannello.';

    protected string $view = 'filament.pages.two-factor-setup';

    public string $otp = '';

    public string $qrCodeSvg = '';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        if ($user->hasConfiguredTwoFactor()) {
            if ((bool) session()->get(ForceTwoFactorSetup::SESSION_KEY, false)) {
                $this->redirect(Dashboard::getUrl(panel: Filament::getCurrentPanel()?->getId()));

                return;
            }

            $this->redirect(TwoFactorChallenge::getUrl(panel: Filament::getCurrentPanel()?->getId()));

            return;
        }

        $secret = session()->get(self::SESSION_SETUP_SECRET);

        if (! is_string($secret) || $secret === '') {
            $secret = Google2FA::generateSecretKey();
            session()->put(self::SESSION_SETUP_SECRET, $secret);
        }

        $this->qrCodeSvg = Google2FA::getQRCodeInline(
            config('app.name', 'VerificaICT'),
            $user->email,
            $secret,
        );
    }

    public function confirmTwoFactorSetup(): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return;
        }

        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $secret = session()->get(self::SESSION_SETUP_SECRET);

        if (! is_string($secret) || $secret === '') {
            Notification::make()
                ->title('Sessione setup 2FA non valida. Ricarica la pagina.')
                ->danger()
                ->send();

            return;
        }

        if (! (bool) Google2FA::verifyKey($secret, $this->otp, 0)) {
            $this->addError('otp', 'Codice OTP non valido o scaduto.');

            return;
        }

        $user->enableTwoFactor($secret);

        Session::forget(self::SESSION_SETUP_SECRET);
        session()->put(ForceTwoFactorSetup::SESSION_KEY, true);

        $this->redirect(Dashboard::getUrl(panel: Filament::getCurrentPanel()?->getId()));
    }
}

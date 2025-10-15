<?php

namespace App\Providers;

use App\Models\AdminUser;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure les routes nommées pour les notifications de réinitialisation de mot de passe
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            if ($notifiable instanceof AdminUser) {
                return url(route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]));
            }

            return url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]));
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       ResetPassword::toMailUsing(function ($notifiable, $token) {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Password LotDate Mold')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kami menerima permintaan reset password untuk akun LotDate Mold kamu.')
            ->action('Buat Password Baru', $url)
            ->line('Tautan ini berlaku 60 menit. Kalau bukan kamu yang meminta, abaikan email ini.')
            ->salutation('LotDate Mold');
    });
    }
}

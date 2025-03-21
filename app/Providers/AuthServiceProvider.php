<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Passport::personalAccessTokensExpireIn(now()->addDays(7));

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return env('FE_APP_URL') . '/auth/password/reset/' . $token . "?email=" . $user->email;
        });

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            error_log($url);
            error_log(env('APP_URL'));
            error_log(env('FE_APP_URL'));
            $url = str_replace(env('APP_URL') . '/verify-email', env('FE_APP_URL') . '/verify-email', $url);
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email Address', $url);
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Set locale untuk Carbon (tanggal bahasa Indonesia)
        \Carbon\Carbon::setLocale('id');

        // Di server online semua tautan dan pengalihan harus memakai HTTPS
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

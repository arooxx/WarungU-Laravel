<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Set locale untuk Carbon (tanggal bahasa Indonesia)
        \Carbon\Carbon::setLocale('id');
    }
}

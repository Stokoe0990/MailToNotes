<?php

namespace Stokoe\MailToNotes\Providers;

use Illuminate\Support\ServiceProvider;

class MailToNotesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/Routes.php');
        $this->publishes([
            __DIR__.'/../Database/Migrations/' => database_path('migrations'),
        ], 'migrations');
    }
}

<?php

namespace FittieApplications\Google;

use Fittie\Component\Application\Registrar\ApplicationRegistrar;
use FittieApplications\Google\Application\GoogleApplication;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        app()->make(ApplicationRegistrar::class)
            ->add(new GoogleApplication());
    }
}

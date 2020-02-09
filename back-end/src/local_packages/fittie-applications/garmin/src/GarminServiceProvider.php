<?php


namespace FittieApplications\Garmin;

use Fittie\Component\Application\Registrar\ApplicationRegistrar;
use FittieApplications\Garmin\Application\GarminApplication;
use Illuminate\Support\ServiceProvider;

class GarminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->get(ApplicationRegistrar::class)->add(new GarminApplication());
    }
}

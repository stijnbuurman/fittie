<?php

namespace FittieApplications\Withings;

use Fittie\Component\Application\Registrar\ApplicationRegistrar;
use FittieApplications\Withings\Application\WithingsApplication;
use Illuminate\Support\ServiceProvider;

class WithingsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        app()->make(ApplicationRegistrar::class)
            ->add(new WithingsApplication());
    }
}

<?php

namespace Fittie\Component\Application\ServiceProvider;

use Fittie\Component\Analytics\Exception\InvalidMetricType;
use Fittie\Component\Analytics\Metric\MetricType;
use Fittie\Component\Application\Entity\ApplicationConnectionID;
use Fittie\Component\Application\Registrar\ApplicationRegistrar;
use Fittie\Component\Application\Repository\SqlApplicationConnectionRepository;
use Fittie\Component\Application\Repository\ApplicationConnectionRepositoryInterface;
use Fittie\Component\User\Entity\UserID;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    public $bindings = [
        ApplicationConnectionRepositoryInterface::class => SqlApplicationConnectionRepository::class,
    ];

    public $singletons = [
        ApplicationRegistrar::class => ApplicationRegistrar::class
    ];

    public function register()
    {
        $this->app->register(ApplicationEventServiceProvider::class);


        Route::bind('application', function ($value) {
            /** @var ApplicationRegistrar $applicationRegistrar */
            $applicationRegistrar = app()->make(ApplicationRegistrar::class);

            return $applicationRegistrar->get($value);
        });

        Route::bind('applicationConnectionID', function ($value) {
            /** @var ApplicationConnectionRepositoryInterface $repository */
            $repository = app()->make(ApplicationConnectionRepositoryInterface::class);

            return $repository->get(new UserID(auth()->id()), new ApplicationConnectionID($value));
        });

        Route::bind('metricType', function ($value) {
            if (!method_exists(MetricType::class, $value)) {
                throw new InvalidMetricType();
            }

            return MetricType::$value();
        });
    }
}

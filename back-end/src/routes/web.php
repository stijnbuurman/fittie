<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Fittie\Component\Analytics\Controller\AnalyticsController;
use Fittie\Component\ApplicationAuth\UserPassword\Controller\UserPasswordController;
use Fittie\Component\Application\Controller\ApplicationController;
use Fittie\Component\ApplicationAuth\OAuth2\Controller\OAuth2Controller;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect(route('applications'));
    }

    return redirect(route('login'));
});

Route::get('/fittie/applications', [ApplicationController::class, 'listApplications'])->name('applications');
Route::get('/fittie/application/{application}/create', [ApplicationController::class, 'createApplicationConnection'])->name('create-application');
Route::get('/fittie/application/{applicationConnectionID}/delete', [ApplicationController::class, 'deleteApplicationConnection'])->name('delete-application');

Route::get('/fittie/analytics/{metricType}/{applicationConnectionID}', [AnalyticsController::class, 'getAnalytics'])->name('analytics');

Route::get('/user-password/{application}/create', [UserPasswordController::class, 'createUserPassword'])->name('auth-user-password');
Route::post('/user-password/{application}/create', [UserPasswordController::class, 'postCreateUserPassword'])->name('post-auth-user-password');
Route::get('/oauth/{application}/request-consent', [OAuth2Controller::class, 'requestConsent'])->name('auth-oauth2');
Route::get('/oauth/{application}/redirect', [OAuth2Controller::class, 'handleRedirect']);

Auth::routes();

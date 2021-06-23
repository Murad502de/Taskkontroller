<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\amoCRMredirectController;
use App\Http\Controllers\Api\Auth\AuthorizationController;
use App\Http\Controllers\Api\UserStatusesController;
use App\Http\Controllers\Api\SettingsController;

// Redirect from amoCRM
Route::get( '/redirect', [ amoCRMredirectController::class, 'redirect' ] );
Route::get( '/redirect/clean/{subdomain}', [ amoCRMredirectController::class, 'deleteData' ] );

// Auth
Route::post( '/amoAuth/login', [ AuthorizationController::class, 'login' ] );
Route::post( '/amoAuth/logout', [ AuthorizationController::class, 'logout' ] );

// UserStatuses
Route::get( '/getUserStatuses', [ UserStatusesController::class, 'get' ] );
Route::post( '/setUserStatuses', [ UserStatusesController::class, 'set' ] );

// Settings
Route::get( '/getSettings', [ SettingsController::class, 'get' ] );
Route::post( '/setSettings', [ SettingsController::class, 'set' ] );
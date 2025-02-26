<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enums\Applications;
use App\Enums\Settings;
use App\Services\GeoLocationService;
use App\PiniaStation\Facades\PiniaLoader;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/v1/applications', function (Request $request) {
    PiniaLoader::load('dashboard', 'applications');

    return PiniaLoader::toApiResponse();
});

Route::get('/v1/settings', function (Request $request) {
    PiniaLoader::load('settings', 'all');

    return PiniaLoader::toApiResponse();
});

Route::get('/v1/projects', function (Request $request) {
    PiniaLoader::load('projects', 'all');

    return PiniaLoader::toApiResponse();
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

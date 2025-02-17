<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enums\Applications;
use App\Enums\Settings;
use App\Services\GeoLocationService;

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
    $applications = collect(Applications::cases())->map(fn($app) => [
        'label' => $app->label(),
        'value' => $app->value,
        'application' => $app->isApplication(),
        'action' => $app->action(),
        'left' => $app->position()['left'] ?? 100,
        'top' => $app->position()['top'] ?? 100,
        'width' => $app->position()['width'] ?? 300,
        'height' => $app->position()['height'] ?? 300,
    ]);

    return response()->json($applications);
});

Route::get('/v1/settings', function (Request $request) {
    $settings = collect(Settings::cases())->map(fn($app) => [
        'label' => $app->label(),
        'value' => $app->value,
    ]);

    return response()->json($settings);
    // dd($request->userAgent());

    // return response()->json($applications);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

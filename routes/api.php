<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enums\Applications;
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
    ]);

    return response()->json($applications);
});

Route::get('/v1/settings', function (Request $request) {
    // dd($request->userAgent());

    // return response()->json($applications);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

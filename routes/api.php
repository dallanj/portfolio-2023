<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ProjectController;
use App\Http\Controllers\api\MediaController;
use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

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

Route::prefix('v1')->group(function () {
    Route::get('/applications', function (Request $request) {
        PiniaLoader::load('dashboard', 'applications');

        return PiniaLoader::toApiResponse();
    });

    Route::get('/settings', function (Request $request) {
        PiniaLoader::load('settings', 'all');

        return PiniaLoader::toApiResponse();
    });

    Route::get('/projects', function (Request $request) {
        PiniaLoader::load('projects', 'all');

        return PiniaLoader::toApiResponse();
    });

    Route::prefix('/projects')->controller(ProjectController::class)->group(function () {
        Route::get('/', 'search');
        Route::get('/{project:hash}/media', 'media');
    });

    Route::resource('projects', ProjectController::class)->only([
        'store', 'update', 'destroy'
    ])->middleware([HandlePrecognitiveRequests::class]);

    Route::resource('media', MediaController::class)->only([
        'store', 'update', 'destroy', 'show'
    ]);

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});

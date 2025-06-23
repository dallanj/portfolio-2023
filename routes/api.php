<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\ResumeController;
use App\Http\Controllers\Api\ContactController;
use App\PiniaStation\Facades\PiniaLoader;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use App\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
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
        PiniaLoader::load('dashboard', 'all');

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
        Route::post('/{project:hash}/media', 'media');
    });

    Route::resource('projects', ProjectController::class)->only([
        'store', 'update', 'destroy'
    ])->middleware([HandlePrecognitiveRequests::class]);

    Route::resource('media', MediaController::class)->only([
        'store', 'update', 'destroy', 'show'
    ]);

    Route::resource('tags', TagController::class)->only([
        'store', 'update', 'destroy'
    ])->middleware([HandlePrecognitiveRequests::class]);

    Route::prefix('/tags')->controller(TagController::class)->group(function () {
        Route::get('/', 'search');
    });

    Route::resource('resumes', ResumeController::class)
        ->only(['store', 'update', 'destroy', 'show'])
        ->middleware([HandlePrecognitiveRequests::class])
        ->withoutMiddleware([
            TrimStrings::class,
            ConvertEmptyStringsToNull::class
        ]);

    Route::prefix('/resumes')
        ->controller(ResumeController::class)
        ->group(function () {
            Route::get('/', 'search');
            Route::post('/publish', 'publish');
            Route::post('/draft', 'draft');
            Route::post('/bulk-delete', 'bulkDelete')
        ->withoutMiddleware([
            TrimStrings::class,
            ConvertEmptyStringsToNull::class
        ]);
    });

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });


    Route::get('/contacts/pgp', [ContactController::class, 'getPublicKey']);
    Route::apiResource('contacts', ContactController::class);
    Route::prefix('/contacts')->controller(ContactController::class)->group(function () {
        Route::get('/', 'search');
        Route::post('/mark-read', 'markRead');
        Route::post('/mark-unread', 'markUnread');
        Route::post('/mark-important', 'markImportant');
        Route::post('/bulk-delete', 'bulkDelete');
    });
});

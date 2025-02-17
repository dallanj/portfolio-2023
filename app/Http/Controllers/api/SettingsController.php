<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Main page for dashboard
     *
     * @return \Inertia\Response
     */
    public function getUserAgent(Request $request)
    {
        // dd($request->userAgent());
        // return Inertia::render('Dashboard', [
        //     'laravel' => \Illuminate\Foundation\Application::VERSION,
        //     'php' => PHP_VERSION,
        // ]);
    }
}

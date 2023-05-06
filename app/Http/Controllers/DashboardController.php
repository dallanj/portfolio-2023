<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Main page for dashboard
     *
     * @return \Inertia\Response
     */
    public function dashboardPage()
    {
        return Inertia::render('Dashboard', [
            'laravel' => \Illuminate\Foundation\Application::VERSION,
            'php' => PHP_VERSION,
        ]);
    }
}

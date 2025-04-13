<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\PiniaStation\Facades\PiniaLoader;

class DashboardController extends Controller
{
    /**
     * Main page for dashboard
     *
     * @return \Inertia\Response
     */
    public function dashboardPage()
    {
        PiniaLoader::load('dashboard', 'all');

        return inertia('Dashboard', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }
}

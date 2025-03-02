<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;
use App\PiniaStation\Facades\PiniaLoader;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Projects/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Projects/Create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return inertia('Projects/Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        PiniaLoader::load('projects', 'active', $project);

        return inertia('Projects/Edit', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }
}

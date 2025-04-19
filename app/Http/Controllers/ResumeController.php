<?php

namespace App\Http\Controllers;

use App\Models\Resume;
use App\PiniaStation\Facades\PiniaLoader;

class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Resumes/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Resumes/Create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resume $resume)
    {
        return inertia('Resumes/Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resume $resume)
    {
        PiniaLoader::load('resumes', 'active', $resume);

        return inertia('Resumes/Create', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }
}

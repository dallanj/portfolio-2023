<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Inertia\Inertia;
use App\PiniaStation\Facades\PiniaLoader;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Tags/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Tags/Create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        return inertia('Tags/Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        PiniaLoader::load('tags', 'active', $tag);

        return inertia('Tags/Create', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }
}

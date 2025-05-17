<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\PiniaStation\Facades\PiniaLoader;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Contacts/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Contacts/Create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return inertia('Contacts/Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        PiniaLoader::load('contacts', 'active', $contact);

        return inertia('Contacts/Create', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }
}

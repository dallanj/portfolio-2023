<?php

namespace App\Http\Controllers\api;

use App\Models\Contact;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\PiniaStation\Facades\PiniaLoader;
use App\Http\Requests\BulkMarkReadRequest;
use App\Http\Requests\BulkDeleteContactsRequest;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search()
    {
        PiniaLoader::load('contacts', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create($request->validated());

        PiniaLoader::load('contacts', 'all');

        return inertia('Contacts/Index', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreContactRequest $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        PiniaLoader::load('contacts', 'all');

        return inertia('Contacts/Index', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }

    /**
     * Mark message as read for the specified resources from storage.
     */
    public function markRead(BulkMarkReadRequest $request)
    {
        $ids = $request->input('ids', []);
        Contact::whereIn('id', $ids)->update(['is_read' => true]);

        PiniaLoader::load('contacts', 'all');

        return inertia('Contacts/Index', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }

    /**
     * Remove the mass resources from storage.
     */
    public function bulkDelete(BulkDeleteContactsRequest $request)
    {
        $ids = $request->input('ids', []);
        Contact::whereIn('id', $ids)->delete();

        PiniaLoader::load('contacts', 'all');

        return inertia('Contacts/Index', [
            'pinia' => PiniaLoader::toJson()
        ]);
    }

    /**
     * Display PGP public key from storage.
     */
    public function getPublicKey()
    {
        PiniaLoader::load('contacts', 'publicKey');

        return PiniaLoader::toApiResponse();
    }
}

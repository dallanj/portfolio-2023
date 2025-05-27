<?php

namespace App\Http\Controllers\api;

use App\Models\Contact;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\PiniaStation\Facades\PiniaLoader;
use App\Http\Requests\BulkMarkReadRequest;
use App\Http\Requests\BulkDeleteContactsRequest;
use App\Services\PgpEncryptorService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

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
        $validated = $request->validated();

        // Encrypt the message
        $pgp = new PgpEncryptorService(null);
        $validated['message'] = $pgp->encrypt($validated['message']);
        // dd($validated);
        

        // dd($encryptedMessage);
        $validated['message'] = Crypt::encryptString($validated['message']);
        $contact = Contact::create($validated);

        PiniaLoader::load('contacts', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        PiniaLoader::load('contacts', 'active');

        return PiniaLoader::toApiResponse();
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

        return PiniaLoader::toApiResponse();
    }

    /**
     * Mark message as read for the specified resources from storage.
     */
    public function markRead(BulkMarkReadRequest $request)
    {
        $ids = $request->input('ids', []);
        Contact::whereIn('id', $ids)->update(['is_read' => true]);

        PiniaLoader::load('contacts', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Mark message as unread for the specified resources from storage.
     */
    public function markUnread(BulkMarkReadRequest $request)
    {
        $ids = $request->input('ids', []);
        Contact::whereIn('id', $ids)->update(['is_read' => false]);

        PiniaLoader::load('contacts', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Mark message as important for the specified resources from storage.
     */
    public function markImportant(BulkMarkReadRequest $request)
    {
        $ids = $request->input('ids', []);
        Contact::whereIn('id', $ids)->update(['is_important' => true]);

        PiniaLoader::load('contacts', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Remove the mass resources from storage.
     */
    public function bulkDelete(BulkDeleteContactsRequest $request)
    {
        $ids = $request->input('ids', []);
        Contact::whereIn('id', $ids)->delete();

        PiniaLoader::load('contacts', 'all');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Display PGP public key from storage.
     */
    public function getPublicKey()
    {
        PiniaLoader::load('contacts', 'publicKey');

        return PiniaLoader::toApiResponse();
    }

    /**
     * Decrypt PGP contact message.
     */
    public function decrypt()
    {
        
        PiniaLoader::load('contacts', 'publicKey');

        return PiniaLoader::toApiResponse();
    }
}

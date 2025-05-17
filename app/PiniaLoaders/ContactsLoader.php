<?php

namespace App\PiniaLoaders;

use App\Models\Contact;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\Storage;
use App\Filters\Searchable;
use App\Filters\Sortable;

class ContactsLoader
{
    /**
     * Get all contacts
     * 
     * @return Collection
     */
    public function all()
    {
        $contacts = Pipeline::send(Contact::query())
            ->through([
                Searchable::class,
                Sortable::class,
            ])
            ->thenReturn();

        if (request()->boolean('paginate', true)) {
            $contacts = $contacts->paginate(request()->per_page ?? 8);
        } else {
            $contacts = $contacts->get();
        }
        
        if ($contacts instanceof \Illuminate\Pagination\Paginator || $contacts instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            // return (new ProjectsCollection($projects))->resolve(request());
            return $contacts;
        }
    
        // If not paginated, transform manually
        // return ProjectResource::collection($projects)->resolve(request());  
        return $contacts;
    }

    /**
     * Get the selected contact
     * 
     * @param Contact $contact
     * 
     * @return Collection
     */
    public function active(Contact $contact)
    {
        return $contact;
    }

    /**
     * Get the pubic key
     * 
     * @return string
     */
    public function publicKey()
    {
        $path = Storage::path(config('pgp.public_key_path'));//storage_path('app/keys/public.asc');

        if (!file_exists($path)) {
            abort(404, 'Error, PGP Public not found.');
        }

        return file_get_contents($path);
    }
};

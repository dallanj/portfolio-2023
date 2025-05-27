<?php

namespace App\Observers;

use App\Models\Contact;
use App\Models\ContactLog;
use Illuminate\Support\Facades\Log;
use App\Services\GeoLocationService;
use Illuminate\Support\Facades\DB;

class ContactObserver
{
    /**
     * Handle the event.
     */
    public function handle(Contact $contact): void
    {
        try {
            DB::beginTransaction();

            $user_agent = request()->userAgent();
            $ip_address = GeoLocationService::getPublicIpAddress();

            $log = new ContactLog([
                'contact_id'    => $contact->id,
                'message_sent'  => true,
                'ip'            => $ip_address,
                'user_agent'    => $user_agent,
            ]);

            $data = GeoLocationService::getLocation($ip_address);

            if (is_array($data)) {
                foreach ($data as $key => $location) {
                    $log->{$key} = $location;
                }
            }
            
            $log->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Unable to save contact log: ' . $e->getMessage());
        }
    }
}

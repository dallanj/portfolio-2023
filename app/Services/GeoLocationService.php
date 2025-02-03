<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /** @var $location_api_url */
    protected static $location_api_url;
    
    /** @var $public_ip_api_url */
    protected static $public_ip_api_url;

    /**
     * Get public IP address of the client.
     */
    public static function getPublicIpAddress(): string
    {
        self::$public_ip_api_url = config('services.geolocation.ip');

        $client_ip = request()->ip();

        if (config('app.env') === 'production') {
            return $client_ip;
        }

        $response = Http::get(self::$public_ip_api_url);

        if ($response->successful()) {
            $data = $response->json();
            return $data['ip'] ?? $client_ip;
        }

        Log::error('Public IP Lookup Error', ['status' => $response->status(), 'body' => $response->body()]);
        return $client_ip;
    }

    /**
     * Get location data for a given IP address.
     *
     * @param string $ip_ddress
     */
    public static function getLocation(string $ip_ddress)
    {
        self::$location_api_url = config('services.geolocation.location');

        $response = Http::get(self::$location_api_url . $ip_ddress, [
            'fields' => '17030137'
        ]);

        if ($response->successful()) {
            Log::info('IP API Response', ['response' => $response->body()]);
            return unserialize($response->body());
        }

        Log::error('IP API Error', ['status' => $response->status(), 'body' => $response->body()]);
        return [
            'status' => 'fail',
            'message' => 'Unable to retrieve location data.'
        ];
    }
}

<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $client;

    public function __construct()
    {


        $this->client = new Client([
            'base_uri' => env('USER_AUTH_SERVICE_URL'), //url is http://127.0.0.1:8000 
        ]);
    }


    public function validateToken($token)
    {
        try {
            $response = $this->client->request('GET', '/api/user', [
                'headers' => ['Authorization' => 'Bearer ' . $token]
            ]);

            Log::info('Response from user service:', ['body' => $response->getBody()->getContents()]);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Error calling user service:', ['error' => $e->getMessage()]);
            return null;
        }
    }
}

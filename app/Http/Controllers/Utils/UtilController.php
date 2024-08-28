<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UtilController extends Controller
{
    
    public function apiDni($dni)
    {
        
        try {
            $url = "https://apiperu.dev/api/dni/".$dni;
            $client = new \GuzzleHttp\Client(['verify'=>false]);
            $token = 'c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d';
            $response = $client->get($url, [
                'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'Authorization' => "Bearer {$token}"
                        ]
            ]);
            $estado     =   $response->getStatusCode();
            $data       =   json_decode($response->getBody()->getContents());

            
            return response()->json(['success'=>true,'data'=>$data]);
        } catch (\Throwable $th) {
            return response()->json(['success'=>false,'data'=>$th->getMessage()]);
        }

    }

}

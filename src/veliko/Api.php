<?php

namespace App\veliko;


class Api
{

    public function getApi(string $url, string $method, string $token): String
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $_ENV["API_VELIKO_URL"].$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => "",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.$token,
                'Cookie: PHPSESSID=78dc5c69e50148e8b7259dbfb06b4bff'
            )

        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;

    }


}
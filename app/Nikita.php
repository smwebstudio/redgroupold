<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Psr7\Request;

class Nikita
{
    protected $api_url = 'http://45.131.124.7';
    protected $originator = 'RED GROUP';

    public function send($recipient, $message)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://45.131.124.7/broker-api/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                                    "messages":{
                                        "recipient":'.$recipient.',
                                        "priority":2,
                                        "sms":{
                                            "originator":'.$this->originator.',
                                            "content":{
                                                "text":'.$message.'
                                                }
                                        },
                                        "message-id":'.time().'
                                    }
                                }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic cmVkZ3JvdXA6UkVEMjM1',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

<?php


namespace App\Services;


use GuzzleHttp\Client;

class HttpResponseService
{
    private $HttpClient;

    public function __construct($base_uri)
    {
        $this->HttpClient = new Client(['base_uri' => $base_uri]);
    }

    protected function getRequest($uri)
    {
        $response = $this->HttpClient->request('GET', $uri);

        return json_decode($response->getBody()->getContents());
    }

}

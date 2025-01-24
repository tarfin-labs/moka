<?php

namespace Tarfin\Moka;

use Illuminate\Support\Facades\Http;

abstract class MokaRequest
{
    protected string $baseUrl;

    protected array $credentials;

    public function __construct()
    {
        $this->baseUrl = config('moka.sandbox_mode')
            ? config('moka.sandbox_url')
            : config('moka.production_url');

        $dealerCode = config('moka.dealer_code');
        $username = config('moka.username');
        $password = config('moka.password');

        $checkKey = hash('sha256', $dealerCode.'MK'.$username.'PD'.$password);

        $this->credentials = [
            'DealerCode' => $dealerCode,
            'Username' => $username,
            'Password' => $password,
            'CheckKey' => $checkKey,
        ];
    }

    protected function sendRequest(string $endpoint, array $requestData): array
    {
        $url = $this->baseUrl.$endpoint;

        $data = array_merge(
            [
                'PaymentDealerAuthentication' => $this->credentials,
            ],
            $requestData
        );

        $response = Http::acceptJson()->post($url, $data);

        return $response->json();
    }
}

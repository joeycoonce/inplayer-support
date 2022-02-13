<?php

namespace SocialPiranha\InPlayerSupport\Services;

use SocialPiranha\InPlayerSupport\Services\Traits\CanBeFaked;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SocialPiranha\InPlayerSupport\Services\Resources\AssetResource;
use SocialPiranha\InPlayerSupport\Contracts\ResourceContract;
use SocialPiranha\InPlayerSupport\Contracts\ServiceContract;
use Illuminate\Support\Facades\Cache;
use SocialPiranha\InPlayerSupport\Services\Exceptions\InPlayerRequestException;

class InPlayerService implements ServiceContract
{
    use CanBeFaked;

    public function __construct(
        public readonly string $url,
        public readonly string $client_id,
        public readonly string $client_secret,
        public readonly string $merchant_uuid,
        public readonly string $merchant_password,
    ) {}

    public function makeRequest(): PendingRequest
    {
        $request = Http::baseUrl(
            url: $this->url, 
        );

        return $request;
    }

    private function getNewAccessToken(): string
    {
        $request = $this->makeRequest();

        $response = $request
            ->asForm()
            ->post(
                url: '/accounts/authenticate',
                data: [
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'grant_type' => 'client_credentials',
                ]
            );

        // dd($response->json());

        if ($response->failed()) {
            throw new InPlayerRequestException(
                response: $response,
            );
        }

        return $response->json()['access_token'];
    }

    public function accessToken(): string
    {
        return Cache::remember(
            key: 'inplayer.access_token',
            ttl: 2592000,
            callback: fn() => $this->getNewAccessToken(),
        );
    }

    public function assets(): ResourceContract
    {
        return new AssetResource(
            service: $this,
        );
    }
}
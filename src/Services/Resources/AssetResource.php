<?php

namespace SocialPiranha\InPlayerSupport\Services\Resources;

// use SocialPiranha\InPlayerSupport\Services\DataObjects\Asset;
// use SocialPiranha\InPlayerSupport\Services\DataObjects\AccessFee;
use SocialPiranha\InPlayerSupport\Services\Exceptions\InPlayerRequestException;
// use SocialPiranha\InPlayerSupport\Services\GitHub\Factories\OwnerFactory;
use SocialPiranha\InPlayerSupport\Services\Factories\AssetFactory;
use SocialPiranha\InPlayerSupport\Services\Factories\AccessFeeFactory;
use SocialPiranha\InPlayerSupport\Services\Requests\CreateAssetRequest;
use SocialPiranha\InPlayerSupport\Services\Requests\AccessFeeRequest;
use Illuminate\Support\Collection;
use SocialPiranha\InPlayerSupport\Contracts\ResourceContract;
use SocialPiranha\InPlayerSupport\Contracts\ServiceContract;
use SocialPiranha\InPlayerSupport\Contracts\DataObjectContract;

class AssetResource implements ResourceContract
{
    public function __construct(
        private readonly ServiceContract $service,
    ) {}

    public function service(): ServiceContract
    {
        return $this->service;
    }

    public function asset(int $id): DataObjectContract
    {
        $request = $this->service->makeRequest();

        $response = $request
            ->withToken($this->service->accessToken())
            ->get(
                url: "/v2/items/{$this->service->merchant_uuid}/{$id}",
            );

        if ($response->failed()) {
            throw new InPlayerRequestException(
                response: $response,
            );
        }

        return AssetFactory::make(
            attributes: (array) $response->json(),
        );
    }

    public function createAsset(CreateAssetRequest $requestBody): DataObjectContract
    {
        $request = $this->service->makeRequest();

        $response = $request
            ->asForm()
            ->withToken($this->service->accessToken())
            ->post(
                url: "/v2/items",
                data: $requestBody->toData(),
            );

        if ($response->failed()) {
            throw new InPlayerRequestException(
                response: $response,
            );
        }

        return AssetFactory::make(
            attributes: (array) $response->json(),
        );
    }

    public function updateAsset(int $id, array $data): DataObjectContract
    {
        $request = $this->service->makeRequest();

        $response = $request
            ->asForm()
            ->withToken($this->service->accessToken())
            ->patch(
                url: "/v2/items/{$id}",
                data: $data,
            );

        if ($response->failed()) {
            throw new InPlayerRequestException(
                response: $response,
            );
        }

        return AssetFactory::make(
            attributes: (array) $response->json(),
        );
    }

    public function accessFees(int $id): Collection
    {
        $request = $this->service->makeRequest();

        $response = $request
            ->withToken($this->service->accessToken())
            ->get(
                url: "/v2/items/{$id}/access-fees",
            );

        if ($response->failed()) {
            throw new InPlayerRequestException(
                response: $response,
            );
        }

        return $response->collect()->map(fn(array $accessFee) => AccessFeeFactory::make(
            attributes: $accessFee,
        ));
    }

    public function accessFee(int $id, int $fee_id): DataObjectContract
    {
        $request = $this->service->makeRequest();

        $response = $request
            ->withToken($this->service->accessToken())
            ->get(
                url: "/v2/items/{$id}/access-fees/{$fee_id}",
            );

        if ($response->failed()) {
            throw new InPlayerRequestException(
                response: $response,
            );
        }

        return AccessFeeFactory::make(
            attributes: (array) $response->json(),
        );
    }

    public function createAccessFee(int $id, AccessFeeRequest $requestBody): DataObjectContract
    {
        $request = $this->service->makeRequest();

        $response = $request
            ->asForm()
            ->withToken($this->service->accessToken())
            ->post(
                url: "/v2/items/{$id}/access-fees",
                data: $requestBody->toData(),
            );

        if ($response->failed()) {
            throw new InPlayerRequestException(
                response: $response,
            );
        }

        return AccessFeeFactory::make(
            attributes: (array) $response->json(),
        );
    }

    public function updateAccessFee(int $id, int $fee_id, AccessFeeRequest $requestBody): DataObjectContract
    {
        $request = $this->service->makeRequest();

        $response = $request
            ->asForm()
            ->withToken($this->service->accessToken())
            ->put(
                url: "/v2/items/{$id}/access-fees/{$fee_id}",
                data: $requestBody->toData(),
            );

        if ($response->failed()) {
            throw new InPlayerRequestException(
                response: $response,
            );
        }

        return AccessFeeFactory::make(
            attributes: (array) $response->json(),
        );
    }
}
<?php

namespace SocialPiranha\InPlayerSupport\Services\DataObjects;

use Carbon\Carbon;
use SocialPiranha\InPlayerSupport\Contracts\DataObjectContract;

class AccessFee implements DataObjectContract
{
    /**
     * @param int $id
     * @param float $amount
     * @param string $currency
     * @param string|null $description
     * @param Asset $asset
     * @param Carbon $created
     */
    public function __construct(
        public readonly int $id,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $description,
        public readonly Asset $asset,
        public readonly Carbon $starts_at,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'asset' => $this->asset->toArray(),
            'starts_at' => $this->starts_at->toDateString(),
        ];
    }
}
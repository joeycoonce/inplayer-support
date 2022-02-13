<?php

namespace SocialPiranha\InPlayerSupport\Services\Requests;

use Carbon\Carbon;

final class AccessFeeRequest
{
    public function __construct(
        public readonly int $access_type_id,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $description,
        public readonly Carbon|null $starts_at,
        public readonly Carbon|null $expires_at,
        public readonly string|null $trial_period_description,
        public readonly int|null $trial_period_quantity,
        public readonly string|null $trial_period_period,
        public readonly int|null $setup_fee_amount,
        public readonly string|null $setup_fee_description,
        public readonly int|null $country_set_id = null,
    ) {}

    public function toData(): array
    {
        return array_merge(
            [
                'access_type_id' => $this->access_type_id,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'description' => $this->description,
            ],
            ($this->starts_at ? ['starts_at' => $this->starts_at->toRfc3339String()] : []),
            ($this->expires_at ? ['expires_at' => $this->expires_at->toRfc3339String()] : []),
            ($this->trial_period_description ? ['trial_period_description' => $this->trial_period_description] : []),
            ($this->trial_period_quantity ? ['trial_period_quantity' => $this->trial_period_quantity] : []),
            ($this->trial_period_period ? ['trial_period_period' => $this->trial_period_period] : []),
            ($this->setup_fee_amount ? ['setup_fee_amount' => $this->setup_fee_amount] : []),
            ($this->setup_fee_description ? ['setup_fee_description' => $this->setup_fee_description] : []),
            ($this->country_set_id ? ['country_set_id' => $this->country_set_id] : []),
        );
    }
}
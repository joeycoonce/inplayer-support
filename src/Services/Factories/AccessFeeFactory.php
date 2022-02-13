<?php

namespace SocialPiranha\InPlayerSupport\Services\Factories;

use SocialPiranha\InPlayerSupport\Services\DataObjects\AccessFee;
use Carbon\Carbon;
use SocialPiranha\InPlayerSupport\Contracts\FactoryContract;

class AccessFeeFactory implements FactoryContract
{
    public static function make(array $attributes): AccessFee
    {
        return new AccessFee(
            id:           intval(data_get($attributes, 'id')),
            amount:       floatval(data_get($attributes, 'amount')),
            currency:     strval(data_get($attributes, 'currency')),
            description:  strval(data_get($attributes, 'description')),
            asset:        AssetFactory::make(
                attributes: (array) data_get($attributes, 'item'),
            ),
            starts_at:    Carbon::createFromTimestampUTC(
                timestamp: intval(data_get($attributes, 'starts_at')),
            )->tz('UTC'),
        );
    }
}
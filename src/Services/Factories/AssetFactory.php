<?php

namespace SocialPiranha\InPlayerSupport\Services\Factories;

use SocialPiranha\InPlayerSupport\Services\DataObjects\Asset;
// use Carbon\Carbon;
use SocialPiranha\InPlayerSupport\Contracts\FactoryContract;

class AssetFactory implements FactoryContract
{
    public static function make(array $attributes): Asset
    {
        return new Asset(
            id:           intval(data_get($attributes, 'id')),
            title:  strval(data_get($attributes, 'title')),
            // created:      Carbon::parse(
            //     time: strval(data_get($attributes, 'created_at')),
            // ),
        );
    }
}
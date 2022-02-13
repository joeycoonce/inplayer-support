<?php

namespace SocialPiranha\InPlayerSupport\Contracts;

interface FactoryContract
{
    /**
     * Pass in a formed array and turn it into a Data Object.
     *
     * @param array $attributes
     * @return DataObjectContract
     */
    public static function make(array $attributes): DataObjectContract;
}
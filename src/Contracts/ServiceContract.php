<?php

namespace SocialPiranha\InPlayerSupport\Contracts;

use Illuminate\Http\Client\PendingRequest;

interface ServiceContract
{
    /**
     * Build the Request.
     *
     * @return PendingRequest
     */
    public function makeRequest(): PendingRequest;
}
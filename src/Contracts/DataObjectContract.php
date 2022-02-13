<?php

namespace SocialPiranha\InPlayerSupport\Contracts;

interface DataObjectContract
{
    /**
     * Return the Data Object back as an array representation.
     *
     * @return array
     */
    public function toArray(): array;
}
<?php

namespace SocialPiranha\InPlayerSupport;

use Illuminate\Support\Facades\Facade;

class InPlayer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'inplayer';
    }
}

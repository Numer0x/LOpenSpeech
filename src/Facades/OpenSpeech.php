<?php

namespace Zacz\LOpenSpeech\Facades;

use Illuminate\Support\Facades\Facade;

class OpenSpeech extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'OpenSpeech';
    }
}

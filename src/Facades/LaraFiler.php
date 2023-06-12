<?php

namespace LaraFiler\Facades;

use Illuminate\Support\Facades\Facade;

class LaraFiler extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'lara-filer';
    }
}
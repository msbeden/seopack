<?php
namespace msbeden\Seopack\Facades;

/**
 * Laravel 8 Seopack
 * @license MIT License
 * @author Mehmet Şaban BEDEN <msbeden@gmail.com>
 * @link https://www.msbeden.tk
 */

use Illuminate\Support\Facades\Facade;

class Seopack extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'seopack';
    }
}

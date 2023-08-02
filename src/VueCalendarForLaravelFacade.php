<?php

namespace Detechtiva\VueCalendarForLaravel;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Detechtiva\VueCalendarForLaravel\Skeleton\SkeletonClass
 */
class VueCalendarForLaravelFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'vue-calendar-for-laravel';
    }
}

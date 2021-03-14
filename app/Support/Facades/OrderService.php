<?php

namespace App\Support\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\OrderService as Service;

/**
 * Make this service accessible and mockable.
 *
 * @package App\Support\Facades
 *
 * @method static Order buildOrder(OrderDto $order)
 */
class OrderService extends Facade
{
    /**
     * Return this Service class
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Service::class;
    }
}

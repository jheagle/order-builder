<?php

namespace App\Support\Facades;

use App\Models\Order;
use Illuminate\Support\Facades\Facade;
use App\Services\OrderService as Service;
use App\Support\DataTransferObjects\OrderDto;

/**
 * Make this service accessible and mockable.
 *
 * @package App\Support\Facades
 *
 * @method static Order buildOrder(OrderDto $orderDto)
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

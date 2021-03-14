<?php

namespace App\Support\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObjectCollection;

/**
 * Store collection of Order Item DTOs to be processed.
 *
 * @package App\Support\DataTransferObjects
 */
class OrderItemsDto extends DataTransferObjectCollection
{
    /**
     * Take incoming array of data and build it into a collection of Order Items
     *
     * @param array $data
     *
     * @return OrderItemsDto
     */
    public static function fromArray(array $data): OrderItemsDto
    {
        return new static(OrderItemDto::arrayOf($data));
    }
}

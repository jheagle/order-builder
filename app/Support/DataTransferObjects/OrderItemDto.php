<?php

namespace App\Support\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * Format all of the required fields for creating an Order Item
 *
 * @package App\Support\DataTransferObjects
 */
class OrderItemDto extends DataTransferObject
{
    /**
     * The product ID.
     */
    public int $productId;

    /**
     * The number of product.
     */
    public int $quantity;
}

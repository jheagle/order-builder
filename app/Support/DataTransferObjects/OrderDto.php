<?php

namespace App\Support\DataTransferObjects;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;
use App\Support\DataTransferObjects\OrderItemsDto;

/**
 * Build required fields for creating an Order
 *
 * @package App\Support\DataTransferObjects
 */
class OrderDto extends DataTransferObject
{
    /**
     * The order number created for the customer.
     */
    public ?int $orderNumber;

    /**
     * The ID of the customer that is making the order.
     */
    public ?int $customerId;

    /**
     * The collection of associated order items.
     *
     * @var \Illuminate\Support\Collection|OrderItemDto[] $orderItems
     */
    public Collection $orderItems;

    /**
     * Take incoming array of data and format to DTO
     *
     * @param array $data
     *
     * @return OrderDto
     */
    public static function fromArray(array $data): OrderDto
    {
        if (is_array($data['orderItems']) && count($data['orderItems']) > 0) {
            $data['orderItems'] = new Collection(OrderItemsDto::fromArray($data['orderItems']));
        }
        return new static($data);
    }
}

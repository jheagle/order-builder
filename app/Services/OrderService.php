<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Support\DataTransferObjects\OrderDto;
use App\Support\DataTransferObjects\OrderItemDto;

/**
 * Manage Orders
 *
 * @package App\Services
 */
class OrderService
{
    /**
     * Take some incoming Order details and build an Order with Order Items
     *
     * @param OrderDto $order
     *
     * @return Order
     */
    public function buildOrder(OrderDto $orderDto): Order
    {
        $order = new Order();
        $order->order_number = $orderDto->orderNumber ?? 0;
        $order->customer_id = $orderDto->customerId ?? 0;
        $order->order_status = Order::STATUS_PENDING;
        $order->total_price = $orderDto->orderItems->reduce(
            fn ($total, $item) => $total + Product::find($item->productId)->price * $item->quantity,
            0
        );
        $order->save();

        $orderDto->orderItems->each(function (OrderItemDto $item) use ($order) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->productId;
            $orderItem->quantity = $item->quantity;
            $orderItem->save();
        });

        return $order;
    }
}

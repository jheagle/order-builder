<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Support\DataTransferObjects\OrderDto;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test service resources used for building orders
 *
 * @package Tests\Feature\Services
 *
 * @group Feature
 * @group Services
 * @group OrderService
 *
 * @coversDefaultClass OrderService
 */
class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public OrderService $service;

    /**
     * Set up these tests.
     *
     * @return void
     */
    final public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->service = new OrderService;
    }

    /**
     * Given an order request
     * When there is only one order item
     * Then a order will be generated and one order item
     *
     * @covers ::buildOrder
     */
    final public function testCreateASingleItemOrder(): void
    {
        $product = Product::find(3);
        $quantity = 1;
        $orderDto = OrderDto::fromArray([
            'orderItems' => [
                [
                    'productId' => $product->id,
                    'quantity' => $quantity,
                ]
            ]
        ]);

        $order = $this->service->buildOrder($orderDto);
        $this->assertDatabaseHas('orders', [
            'order_status' => Order::STATUS_PENDING,
            'total_price' => $quantity * $product->price,
        ]);
        self::assertCount(1, $order->orderItems);
    }

    /**
     * Given an order request
     * When there are multiple order items
     * Then a order will be generated and each order item
     *
     * @covers ::buildOrder
     */
    final public function testCreateAMultiItemOrder(): void
    {
        $productOne = Product::find(1);
        $productTwo = Product::find(2);
        $quantityOne = 3;
        $quantityTwo = 2;
        $orderDto = OrderDto::fromArray([
            'orderItems' => [
                [
                    'productId' => $productOne->id,
                    'quantity' => $quantityOne,
                ],
                [
                    'productId' => $productTwo->id,
                    'quantity' => $quantityTwo,
                ]
            ]
        ]);
        $total = ($productOne->price * $quantityOne) + ($productTwo->price * $quantityTwo);
        $order = $this->service->buildOrder($orderDto);
        $this->assertDatabaseHas('orders', [
            'order_status' => Order::STATUS_PENDING,
            'total_price' => $total,
        ]);
        self::assertCount(2, $order->orderItems);
    }
}

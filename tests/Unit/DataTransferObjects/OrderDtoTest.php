<?php

namespace Tests\Unit\DataTransferObjects;

use TypeError;
use Tests\TestCase;
use App\Support\DataTransferObjects\OrderDto;

/**
 * Test the Order Data Transfer Object
 *
 * @group Unit
 * @group DataTransferObjects
 * @group OrderDto
 *
 * @coversDefaultClass OrderDto
 */
class OrderDtoTest extends TestCase
{
    /**
     * Given an order
     * When no order items exist
     * Then an error will be thrown
     *
     * @covers ::fromArray
     */
    final public function testCreateOrderWithNoOrderItemsFails(): void
    {
        $this->expectException(TypeError::class);
        OrderDto::fromArray([
            'orderItems' => []
        ]);
    }
}

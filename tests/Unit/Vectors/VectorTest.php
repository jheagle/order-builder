<?php

namespace Tests\Unit\Vectors;

use Tests\TestCase;
use App\Vectors\Vector;

/**
 * Test storing and manage a Vector
 *
 * @package Tests\Unit\Vectors
 *
 * @group Unit
 * @group Vectors
 * @group Vector
 *
 * @coversDefaultClass Vector
 */
class VectorTest extends TestCase
{
    /**
     * Given an existing vector
     * When using add with another vector
     * Then a new vector with summed coordinates will be returned.
     *
     * @covers ::add
     */
    final public function testAddVectorCreatesNewVector(): void
    {
        $start = new Vector(0, 3, 2);
        $other = new Vector(5, 1, 4);
        $newVector = $start->add($other);
        self::assertEquals(5, $newVector->x);
        self::assertEquals(4, $newVector->y);
        self::assertEquals(6, $newVector->z);
    }

    /**
     * Given an existing vector
     * When using diff with another vector
     * Then a new vector with delta coordinates will be returned.
     *
     * @covers ::diff
     */
    final public function testDiffVectorCreatesNewVector(): void
    {
        $start = new Vector(0, 3, 2);
        $other = new Vector(5, 1, 4);
        $newVector = $start->diff($other);
        self::assertEquals(5, $newVector->x);
        self::assertEquals(-2, $newVector->y);
        self::assertEquals(2, $newVector->z);
    }

    /**
     * Given an existing vector
     * When using equals with another vector
     * Then it will return true for match, false for mismatch.
     *
     * @covers ::equals
     */
    final public function testEqualsComparesTwoVectors(): void
    {
        $start = new Vector(0, 3, 2);
        self::assertFalse($start->equals(new Vector(5, 1, 4)));
        self::assertTrue($start->equals(new Vector(0, 3, 2)));
    }

    /**
     * Given an existing vector
     * When using getDirection with another vector
     * Then a new vector with coordinates of either 0 or 1 based on difference.
     *
     * @covers ::getDirection
     */
    final public function testGetDirectionReturnsDirectionVector(): void
    {
        $start = new Vector(0, 3, 2);
        $other = new Vector(5, 3, 4);
        $newVector = $start->getDirection($other);
        self::assertEquals(1, $newVector->x);
        self::assertEquals(0, $newVector->y);
        self::assertEquals(1, $newVector->z);
    }

    /**
     * Given an existing vector
     * When using getEndPoint with dimensions vector
     * Then a new vector with final position will be returned.
     *
     * @covers ::getEndPoint
     */
    final public function testGetEndpointReturnsEndPointVector(): void
    {
        $start = new Vector(0, 3, 2);
        $dimensions = new Vector(5, 1, 4);
        $newVector = $start->getEndPoint($dimensions);
        self::assertEquals(4, $newVector->x);
        self::assertEquals(3, $newVector->y);
        self::assertEquals(5, $newVector->z);
    }
}

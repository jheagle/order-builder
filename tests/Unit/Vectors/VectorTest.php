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
    public function testAddVectorCreatesNewVector()
    {
        $start = new Vector(0, 3, 2);
        $other = new Vector(5, 1, 4);
        $newVector = $start->add($other);
        $this->assertEquals(5, $newVector->x);
        $this->assertEquals(4, $newVector->y);
        $this->assertEquals(6, $newVector->z);
    }

    /**
     * Given an existing vector
     * When using diff with another vector
     * Then a new vector with delta coordinates will be returned.
     *
     * @covers ::diff
     */
    public function testDiffVectorCreatesNewVector()
    {
        $start = new Vector(0, 3, 2);
        $other = new Vector(5, 1, 4);
        $newVector = $start->diff($other);
        $this->assertEquals(5, $newVector->x);
        $this->assertEquals(-2, $newVector->y);
        $this->assertEquals(2, $newVector->z);
    }

    /**
     * Given an existing vector
     * When using equals with another vector
     * Then it will return true for match, false for mismatch.
     *
     * @covers ::equals
     */
    public function testEqualsComparesTwoVectors()
    {
        $start = new Vector(0, 3, 2);
        $this->assertFalse($start->equals(new Vector(5, 1, 4)));
        $this->assertTrue($start->equals(new Vector(0, 3, 2)));
    }

    /**
     * Given an existing vector
     * When using getDirection with another vector
     * Then a new vector with coordinates of either 0 or 1 based on difference.
     *
     * @covers ::getDirection
     */
    public function testGetDirectionReturnsDirectionVector()
    {
        $start = new Vector(0, 3, 2);
        $other = new Vector(5, 3, 4);
        $newVector = $start->getDirection($other);
        $this->assertEquals(1, $newVector->x);
        $this->assertEquals(0, $newVector->y);
        $this->assertEquals(1, $newVector->z);
    }

    /**
     * Given an existing vector
     * When using getEndPoint with dimensions vector
     * Then a new vector with final position will be returned.
     *
     * @covers ::getEndPoint
     */
    public function testGetEndpointReturnsEndPointVector()
    {
        $start = new Vector(0, 3, 2);
        $dimensions = new Vector(5, 1, 4);
        $newVector = $start->getEndPoint($dimensions);
        $this->assertEquals(4, $newVector->x);
        $this->assertEquals(3, $newVector->y);
        $this->assertEquals(5, $newVector->z);
    }
}

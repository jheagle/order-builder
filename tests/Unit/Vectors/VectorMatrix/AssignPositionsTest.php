<?php

namespace Tests\Unit\Vectors\VectorMatrix;

use App\Vectors\VectorModel;
use Exception;
use Tests\TestCase;
use App\Vectors\Vector;
use App\Vectors\VectorMatrix;
use Illuminate\Support\Collection;
use App\Services\PrintSheetService;

/**
 * Test placing of a variety of sized print sheet items onto a sheet
 *
 * @package Tests\Unit\Vectors\VectorMatrix
 *
 * @group Feature
 * @group VectorMatrix
 * @group AssignPosition
 *
 * @coversDefaultClass VectorMatrix
 */
class AssignPositionsTest extends TestCase
{
    public PrintSheetService $service;

    /**
     * Set up these tests.
     *
     * @return void
     */
    final public function setUp(): void
    {
        parent::setUp();

        $this->service = new PrintSheetService;
    }

    /**
     * Test various collections of vectors fit as expected.
     *
     * @dataProvider sheetItemProvider
     *
     * @param array $vectorItems
     * @param array $expected
     *
     * @covers ::assignAvailablePositions
     *
     * @throws Exception
     */
    final public function testPositionSet(array $vectorItems, array $expected): void
    {
        $expectedVectors = $this->makeVectorCollection($expected);
        $sheetItemCollection = $this->makeVectorItems($vectorItems);

        (new VectorMatrix($this->service::SHEET_WIDTH, $this->service::SHEET_HEIGHT))
            ->create()
            ->assignAvailablePositions($sheetItemCollection);

        self::assertCount($expectedVectors->count(), $sheetItemCollection);
        self::assertTrue(
            $sheetItemCollection->every(
                fn(VectorModel $sheetItem) => $expectedVectors->contains(
                    fn(Vector $vector) => $vector->equals($sheetItem->getAnchorPoint())
                )
            )
        );
    }

    /**
     * Test provider for testing positions
     *
     * @return int[]
     */
    final public function sheetItemProvider(): array
    {
        return [
            'single 5x2 should be top left' => [
                'vectorItems' => [
                    [5, 2]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 2x5 should be top left' => [
                'vectorItems' => [
                    [2, 5]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 4x4 should be top left' => [
                'vectorItems' => [
                    [4, 4]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 3x3 should be top left' => [
                'vectorItems' => [
                    [3, 3]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 2x2 should be top left' => [
                'vectorItems' => [
                    [2, 2]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'single 1x1 should be top left' => [
                'vectorItems' => [
                    [1, 1]
                ],
                'expected' => [
                    [0, 0]
                ]
            ],
            'use one of each to fill the page' => [
                'vectorItems' => [
                    [5, 2],
                    [2, 5],
                    [4, 4],
                    [3, 3],
                    [2, 2],
                    [1, 1],
                ],
                'expected' => [
                    [0, 0],
                    [5, 0],
                    [0, 2],
                    [7, 0],
                    [7, 3],
                    [4, 2],
                ]
            ],
            'use two of each to fill the page' => [
                'vectorItems' => [
                    [5, 2], [5, 2],
                    [2, 5], [2, 5],
                    [4, 4], [4, 4],
                    [3, 3], [3, 3],
                    [2, 2], [2, 2],
                    [1, 1], [1, 1],
                ],
                'expected' => [
                    [0, 0], [5, 0],
                    [0, 2], [2, 2],
                    [4, 2], [4, 6],
                    [0, 7], [0, 10],
                    [8, 2], [8, 4],
                    [8, 6], [9, 6],
                ]
            ],
            'use three of each to fill the page' => [
                'vectorItems' => [
                    [5, 2], [5, 2], [5, 2],
                    [2, 5], [2, 5], [2, 5],
                    [4, 4], [4, 4], [4, 4],
                    [3, 3], [3, 3], [3, 3],
                    [2, 2], // [2, 2], [2, 2],
                    [1, 1], [1, 1], [1, 1],
                ],
                'expected' => [
                    [0, 0], [5, 0], [0, 2],
                    [5, 2], [7, 2], [0, 4],
                    [2, 7], [6, 7], [0, 11],
                    [2, 4], [4, 11], [7, 11],
                    [0, 9], // There are two [2, 2] that cannot fit, unused area of 8 units
                    [9, 2], [9, 3], [9, 4],
                ]
            ],
            'perfect fit of 2x5 should fill the entire matrix' => [
                'vectorItems' => [
                    [2, 5], [2, 5], [2, 5], [2, 5], [2, 5],
                    [2, 5], [2, 5], [2, 5], [2, 5], [2, 5],
                    [2, 5], [2, 5], [2, 5], [2, 5], [2, 5],
                ],
                'expected' => [
                    [0, 0], [2, 0], [4, 0], [6, 0], [8, 0],
                    [0, 5], [2, 5], [4, 5], [6, 5], [8, 5],
                    [0, 10], [2, 10], [4, 10], [6, 10], [8, 10],
                ]
            ],
            'max 5x2 fit in matrix' => [
                'vectorItems' => [
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                    [5, 2], [5, 2],
                ],
                'expected' => [
                    [0, 0], [5, 0],
                    [0, 2], [5, 2],
                    [0, 4], [5, 4],
                    [0, 6], [5, 6],
                    [0, 8], [5, 8],
                    [0, 10], [5, 10],
                    [0, 12], [5, 12],
                ]
            ],
            'max 4x4 fit in matrix' => [
                'vectorItems' => [
                    [4, 4], [4, 4],
                    [4, 4], [4, 4],
                    [4, 4], [4, 4],
                ],
                'expected' => [
                    [0, 0], [4, 0],
                    [0, 4], [4, 4],
                    [0, 8], [4, 8],
                ]
            ],
            'max 3x3 fit in matrix' => [
                'vectorItems' => [
                    [3, 3], [3, 3], [3, 3],
                    [3, 3], [3, 3], [3, 3],
                    [3, 3], [3, 3], [3, 3],
                    [3, 3], [3, 3], [3, 3],
                    [3, 3], [3, 3], [3, 3],
                ],
                'expected' => [
                    [0, 0], [3, 0], [6, 0],
                    [0, 3], [3, 3], [6, 3],
                    [0, 6], [3, 6], [6, 6],
                    [0, 9], [3, 9], [6, 9],
                    [0, 12], [3, 12], [6, 12],
                ]
            ],
            'max 2x2 fit in matrix' => [
                'vectorItems' => [
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                    [2, 2], [2, 2], [2, 2], [2, 2], [2, 2],
                ],
                'expected' => [
                    [0, 0], [2, 0], [4, 0], [6, 0], [8, 0],
                    [0, 2], [2, 2], [4, 2], [6, 2], [8, 2],
                    [0, 4], [2, 4], [4, 4], [6, 4], [8, 4],
                    [0, 6], [2, 6], [4, 6], [6, 6], [8, 6],
                    [0, 8], [2, 8], [4, 8], [6, 8], [8, 8],
                    [0, 10], [2, 10], [4, 10], [6, 10], [8, 10],
                    [0, 12], [2, 12], [4, 12], [6, 12], [8, 12],
                ]
            ],
            'max 1x1 fit in matrix' => [
                'vectorItems' => [
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                    [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1], [1, 1],
                ],
                'expected' => [
                    [0, 0], [1, 0], [2, 0], [3, 0], [4, 0], [5, 0], [6, 0], [7, 0], [8, 0], [9, 0],
                    [0, 1], [1, 1], [2, 1], [3, 1], [4, 1], [5, 1], [6, 1], [7, 1], [8, 1], [9, 1],
                    [0, 2], [1, 2], [2, 2], [3, 2], [4, 2], [5, 2], [6, 2], [7, 2], [8, 2], [9, 2],
                    [0, 3], [1, 3], [2, 3], [3, 3], [4, 3], [5, 3], [6, 3], [7, 3], [8, 3], [9, 3],
                    [0, 4], [1, 4], [2, 4], [3, 4], [4, 4], [5, 4], [6, 4], [7, 4], [8, 4], [9, 4],
                    [0, 5], [1, 5], [2, 5], [3, 5], [4, 5], [5, 5], [6, 5], [7, 5], [8, 5], [9, 5],
                    [0, 6], [1, 6], [2, 6], [3, 6], [4, 6], [5, 6], [6, 6], [7, 6], [8, 6], [9, 6],
                    [0, 7], [1, 7], [2, 7], [3, 7], [4, 7], [5, 7], [6, 7], [7, 7], [8, 7], [9, 7],
                    [0, 8], [1, 8], [2, 8], [3, 8], [4, 8], [5, 8], [6, 8], [7, 8], [8, 8], [9, 8],
                    [0, 9], [1, 9], [2, 9], [3, 9], [4, 9], [5, 9], [6, 9], [7, 9], [8, 9], [9, 9],
                    [0, 10], [1, 10], [2, 10], [3, 10], [4, 10], [5, 10], [6, 10], [7, 10], [8, 10], [9, 10],
                    [0, 11], [1, 11], [2, 11], [3, 11], [4, 11], [5, 11], [6, 11], [7, 11], [8, 11], [9, 11],
                    [0, 12], [1, 12], [2, 12], [3, 12], [4, 12], [5, 12], [6, 12], [7, 12], [8, 12], [9, 12],
                    [0, 13], [1, 13], [2, 13], [3, 13], [4, 13], [5, 13], [6, 13], [7, 13], [8, 13], [9, 13],
                    [0, 14], [1, 14], [2, 14], [3, 14], [4, 14], [5, 14], [6, 14], [7, 14], [8, 14], [9, 14],
                ]
            ],
        ];
    }

    /**
     * Helper for converting array to Sheet Items
     *
     * @param array $itemDimensions
     *
     * @return Collection
     */
    private function makeVectorItems(array $itemDimensions): Collection
    {
        return Collection::make(array_map(
            function (array $dimensions): VectorModel {
                return new class(...$dimensions) extends VectorModel {
                    public int $x = 0;
                    public int $y = 0;
                    public int $width = 0;
                    public int $height = 0;

                    public function __construct(int $width = 0, int $height = 0)
                    {
                        parent::__construct();
                        $this->width = $width;
                        $this->height = $height;
                    }
                };
            },
            $itemDimensions
        ));
    }

    /**
     * Helper for converting arrays to Vectors
     *
     * @param array $vectorCoordinates
     *
     * @return Collection
     */
    private function makeVectorCollection(array $vectorCoordinates): Collection
    {
        return Collection::make(
            array_map(static function (array $coordinates): Vector {
                return new Vector(...$coordinates);
            }, $vectorCoordinates)
        );
    }
}

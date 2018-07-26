<?php

namespace glen\ThrowableGenerator\Tests;

use Generator;
use glen\ThrowableGenerator\ThrowableGenerator;
use InvalidArgumentException;
use Throwable;

class GeneratorTest extends TestCase
{
    private $processed = [];
    private $throwing = [];
    private $catched = [];

    /**
     * @dataProvider sequenceProvider
     */
    public function testIteratorReceivesAllItems(
        array $items,
        array $expectedProcessed,
        array $expectedThrows,
        array $expectedCatches
    ) {
        $generator = $this->getIterator($items);
        $generator = new ThrowableGenerator($generator);

        foreach ($generator as $item) {
            try {
                $this->processed[] = $item;

                if ($item % 2 === 0) {
                    $this->throwing[] = $item;
                    throw new InvalidArgumentException($item);
                }
            } catch (Throwable $e) {
                $generator->throw($e);
            }
        }
        $this->assertEquals($expectedProcessed, $this->processed);
        $this->assertEquals($expectedThrows, $this->throwing);
        $this->assertEquals($expectedCatches, $this->catched);
    }

    private function getIterator($items): Generator
    {
        foreach ($items as $item) {
            try {
                yield $item;

            } catch (Throwable $e) {
                $this->catched[] = $e->getMessage();
            }
        }
    }

    public function sequenceProvider(): array
    {
        return [
            /**
             * Without the wrapper, processed items are:
             *  [ 1, 2, 4, 6 ]
             */
            [
                range(1, 6),
                [1, 2, 3, 4, 5, 6],
                [2, 4, 6],
                [2, 4, 6],
            ],
            [
                range(1, 9),
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [2, 4, 6, 8],
                [2, 4, 6, 8],
            ],
        ];
    }
}

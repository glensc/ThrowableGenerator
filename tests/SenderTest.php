<?php

namespace glen\ThrowableGenerator\Tests;

use Generator;
use glen\ThrowableGenerator\ThrowableGenerator;

class SenderTest extends TestCase
{
    private $processed = [];
    private $values = [];


    /**
     * @dataProvider sequenceProvider
     */
    public function testSendReceivesAllValues(
        array $items,
        array $expectedProcessed,
        array $expectedValues
    ) {
        $generator = $this->getIterator($items);
        $generator = new ThrowableGenerator($generator);

        foreach ($generator as $item) {
            $this->processed[] = $item;
            $generator->send($item);
        }
        $this->assertEquals($expectedProcessed, $this->processed);
        $this->assertEquals($expectedValues, $this->values);
    }

    private function getIterator($items): Generator
    {
        foreach ($items as $item) {
            $this->values[] = yield $item;
        }
    }

    public function sequenceProvider(): array
    {
        return [
            /**
             * without the wrapper $processed items are:
             *  [1, 3, 5]
             * and $values:
             *  [1, null, 3, null, 5, null]
             */
            [
                range(1, 6),
                [1, 2, 3, 4, 5, 6],
                [1, 2, 3, 4, 5, 6],
            ],
            [
                range(1, 9),
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
        ];
    }
}

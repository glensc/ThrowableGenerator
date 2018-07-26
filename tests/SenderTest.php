<?php

namespace glen\ThrowableGenerator\Tests;

use Generator;
use glen\ThrowableGenerator\ThrowableGenerator;

class SenderTest extends TestCase
{
    private $processed = [];
    private $values = [];

    /**
     * without the wrapper $processed items are:
     *  [1, 3, 5]
     * and $values:
     *  [1, null, 3, null, 5, null]
     */
    public function testSendReceivesAllValues()
    {
        $generator = $this->getIterator(range(1, 6));
        $generator = new ThrowableGenerator($generator);

        foreach ($generator as $item) {
            $this->processed[] = $item;
            $generator->send($item);
        }
        $this->assertEquals([1, 2, 3, 4, 5, 6], $this->processed);
        $this->assertEquals([1, 2, 3, 4, 5, 6], $this->values);
    }

    private function getIterator($items): Generator
    {
        foreach ($items as $item) {
            $this->values[] = yield $item;
        }
    }
}

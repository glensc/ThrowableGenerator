<?php

namespace glen\ThrowableGenerator\Tests;

use glen\ThrowableGenerator\ThrowableGenerator;
use InvalidArgumentException;
use Throwable;

class GeneratorTest extends TestCase
{
    private $processed = [];
    private $throwing = [];
    private $catched = [];

    public function testIteratorReceivesAllItems()
    {
        $generator = $this->getIterator(range(1, 6));
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
        $this->assertEquals([1, 2, 3, 4, 5, 6], $this->processed);
        $this->assertEquals([2, 4, 6], $this->throwing);
        $this->assertEquals([2, 4, 6], $this->catched);
    }

    private function getIterator($items)
    {
        foreach ($items as $item) {
            try {
                yield $item;

            } catch (Throwable $e) {
                $this->catched[] = $e->getMessage();
            }
        }
    }
}

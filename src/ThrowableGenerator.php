<?php

namespace glen\ThrowableGenerator;

use Generator;
use IteratorAggregate;
use Throwable;

/**
 * Makes use of $generator->throw(Exception) for yield to be sequential.
 *
 * The original use of throw loses one yielded value, because it gets returned by throw method call
 *
 * http://php.net/manual/en/generator.throw.php#refsect1-generator.throw-returnvalues
 * https://wiki.php.net/rfc/generators#throwing_into_the_generator
 * https://stackoverflow.com/questions/51382259/why-in-php-using-generatorthrow-omits-yielded-values-after-throw
 *
 * Wrap this class around your original generator:
 * $generator = new ThrowableGenerator($generator);
 */
class ThrowableGenerator implements IteratorAggregate
{
    /** @var Generator */
    private $inner;
    /** @var Throwable */
    private $exception;
    /** @var array */
    private $items = [];

    public function __construct(Generator $inner)
    {
        $this->inner = $inner;
    }

    public function getIterator(): iterable
    {
        foreach ($this->inner as $item) {
            retry:
            yield $item;

            if ($this->exception) {
                $value = $this->inner->throw($this->exception);
                if ($this->inner->valid()) {
                    $this->items[] = $value;
                }
                $this->exception = null;
            }

            if ($this->items) {
                $item = array_shift($this->items);

                goto retry;
            }
        }
    }

    public function throw(Throwable $e)
    {
        $this->exception = $e;
    }

    public function send($value)
    {
        $nextValue = $this->inner->send($value);
        if ($this->inner->valid()) {
            $this->items[] = $nextValue;
        }
    }
}

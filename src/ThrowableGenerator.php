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
    /** @var */
    private $lastException;

    public function __construct(Generator $inner)
    {
        $this->inner = $inner;
    }

    public function getIterator(): iterable
    {
        foreach ($this->inner as $item) {
            retry:
            yield $item;

            if ($this->lastException) {
                $item = $this->inner->throw($this->lastException);
                $this->lastException = null;

                // $item may be missing if the inner generator ended
                if ($this->inner->valid()) {
                    goto retry;
                }
            }
        }
    }

    public function throw(Throwable $e)
    {
        $this->lastException = $e;
    }
}

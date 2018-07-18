# ThrowableGenerator

Generator wrapper for yield be sequential regardless of using Generator::throw.

[Generator::throw] omits next value from generator, instead it makes it return value of `throw()` method.

This is rather inconvenient if you want values be returned by `yield`.

```php
class moo
{
    public function run()
    {
        $generator = $this->getIterator();
        foreach ($generator as $item) {
            try {
                error_log("PROCESS: {$item}");

                if ($item % 2 === 0) {
                    error_log("throwing InvalidArgumentException $item");
                    throw new InvalidArgumentException($item);
                }
            } catch (Throwable $e) {
                $generator->throw($e);
            }
        }
    }

    private function getIterator()
    {
        foreach (range(1, 6) as $item) {
            try {
                yield $item;

            } catch (Throwable $e) {

                $class = get_class($e);
                error_log("GOT[$class] in generator: {$e->getMessage()}");
            }
        }
    }
}

$m = new moo();
$m->run();
```

The above code prints:

```text
PROCESS: 1
PROCESS: 2
throwing InvalidArgumentException 2
GOT[InvalidArgumentException] in generator: 2
PROCESS: 4
throwing InvalidArgumentException 4
GOT[InvalidArgumentException] in generator: 4
PROCESS: 6
throwing InvalidArgumentException 6
GOT[InvalidArgumentException] in generator: 6
```

See question and discussion on [stackoverflow post]

[Generator::throw]: http://php.net/manual/en/generator.throw.php
[stackoverflow post]: https://stackoverflow.com/questions/51382259/why-in-php-using-generatorthrow-omits-yielded-values-after-throw
# ThrowableGenerator

Generator wrapper for yield be sequential regardless of using [Generator::throw].

From top level view, [Generator::throw] discards next value from generator,
but it's not completely lost, but instead it becames return value of `throw()` method.

This is rather inconvenient if you want values be returned by `yield`.

```php
<?php
 
function generator() {
  foreach (range(1, 6) as $x) {
    try {
      yield $x;
    } catch (Throwable $e) {
      echo " !! exception: {$e->getMessage()}\n";
    }
  }
}

$generator = generator();

foreach ($generator as $x) {
  echo "process: $x\n";
  if ($x % 2 === 0) {
    $generator->throw(new RuntimeException($x));
  }
}
```

The above code prints:

```text
process: 1
process: 2
 !! exception: 2
process: 4
 !! exception: 4
process: 6
 !! exception: 6
```

i.e values `3` and `5` were "lost" by using `throw`. This library makes result to be:

```text
process: 1
process: 2
 !! exception: 2
process: 3
process: 4
 !! exception: 4
process: 5
process: 6
 !! exception: 6
```


See question and discussion on [stackoverflow post]

[Generator::throw]: http://php.net/manual/en/generator.throw.php
[stackoverflow post]: https://stackoverflow.com/questions/51382259/why-in-php-using-generatorthrow-omits-yielded-values-after-throw

To use this class, wrap your original generator with this class:

```php
$generator = new ThrowableGenerator($generator);
```

Note: There is API change, `throw()` will return nothing instead of next value from generator.

# 4.0 -> 5.0

**Backtrace** 

In order to improve performance of matcher `Backtrace` class was replaced `InMemoryBacktrace`
that implements `Backtrace` interface. 

In order to use backtrace provide it directly to `MatcherFactory` or `PHPMatcher` class.

PHPUnit tests require `setBacktrace` method to be used before test:

```php
$this->setBacktrace($backtrace = new InMemoryBacktrace());
$this->assertMatchesPattern('{"foo": "@integer@"}', json_encode(['foo' => 'bar']));
```

**Optional Matchers**

XML and Expression matchers are now optional, in order to use them add following 
dependencies to your composer.json file:

XMLMatcher

```
"openlss/lib-array2xml": "^1.0"
```

ExpressionMatcher

```
symfony/expression-language
```

# 3.x -> 4.0 

Below you can find list of changes between `3.x` and `4.0` versions of PHPMatcher.

**Creating Matcher:** 
```diff
-$factory = new MatcherFactory();
-$matcher = $factory->createMatcher();
+$matcher = new PHPMatcher();
```

**Using Matcher**
```diff
-PHPMatcher::match($value, $pattern, $error)
+$matcher = (new PHPMatcher())->match($value, $pattern);;
```

**Accessing last error/backtrace**
```diff
+$matcher = new PHPMatcher();
+$matcher->match($value, $pattern);
+echo $matcher->error();
+echo $matcher->backtrace();
```
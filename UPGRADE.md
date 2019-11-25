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
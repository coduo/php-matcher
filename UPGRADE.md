# 3.x -> 4.0 

Below you can find list of changes between 3.x and 4.0 versions of PHPMatcher.

### Creating Matcher

```diff
-$factory = new MatcherFactory();
-$this->matcher = $factory->createMatcher();
```

```diff
+$this->matcher = new PHPMatcher();
```

### Simple Matching

```diff
-PHPMatcher::match($value, $pattern, $error)
```

```diff
+$matcher = new PHPMatcher();
+$matcher->match($value, $pattern);
+echo $matcher->error();
```
# Upgrade from 1.1 to 2.0 

* All classes are now marked as final in order to close extra extension points 
* ``Coduo\PHPMatcher\Matcher\CaptureMatcher`` was removed
* ``Coduo\PHPMatcher\Matcher\TypeMatcher`` was removed 
* ``Coduo\PHPMatcher\Matcher\PropertyMatcher`` interface was remved
* Removed ``match`` function, use PHPMatcher facade instead
* Renamed ``@pattern@.notEmpty()`` expander into ``@pattern@.isNotEmpty()``

# Upgrade from 1.0 to 1.1

*No known BC Breakes*
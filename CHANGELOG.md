# 2.0.0-r2

* Added or operator ``@string@||@null@`` - @partikus

# 2.0.0-r1

# 2.0.0-beta

* Openssl/lib-array2xml dependency is now more flexible - @blazarecki 

# 2.0.0-alpha2

* PHPUnit integration - @jakzal

# 2.0.0-alpha1

* Added support for PHP7 
* Updated dependencies to support Symfony3 components
* Added ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\MatchRegexTest`` expander - @blazarecki
* Added ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\IsEmpty`` expander - @blazarecki
* Added PHPMatcher facade in order to improve developers experience
 

# 1.1.0 

* Added pattern expanders mechanism with following expanders: 
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\Contains``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\EndsWith``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\GreaterThan``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\InArray``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\IsDateTime``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\IsEmail``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\IsUrl``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\LowerThan``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\MatchRegex``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\OneOf``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\StartsWith``
    
# 1.0.0

* PHPMatcher initial release with following matchers:
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\ArrayMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\CallbackMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\CaptureMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\ChainMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\ExpressionMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\JsonMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\NullMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\ScalarMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\TypeMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\WildcardMatcher``
    * ``Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander\XmlMatcher``

<?php

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\AST;
use Coduo\PHPMatcher\Exception\Exception;
use Coduo\PHPMatcher\Exception\PatternException;
use Coduo\PHPMatcher\Exception\UnknownExpanderException;
use Coduo\PHPMatcher\Matcher\Pattern;
use Coduo\PHPMatcher\Parser\ExpanderInitializer;

final class Parser
{
    const NULL_VALUE = 'null';

    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var ExpanderInitializer
     */
    private $expanderInitializer;

    /**
     * @param Lexer $lexer
     */
    public function __construct(Lexer $lexer, ExpanderInitializer $expanderInitializer)
    {
        $this->lexer = $lexer;
        $this->expanderInitializer = $expanderInitializer;
    }

    /**
     * @param string $pattern
     * @return bool
     */
    public function hasValidSyntax($pattern)
    {
        try {
            $this->getAST($pattern);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $pattern
     * @throws UnknownExpanderException
     * @return Pattern\TypePattern
     */
    public function parse($pattern)
    {
        $AST = $this->getAST($pattern);
        $pattern = new Pattern\TypePattern((string) $AST->getType());
        foreach ($AST->getExpanders() as $expander) {
            $pattern->addExpander($this->expanderInitializer->initialize($expander));
        }

        return $pattern;
    }

    /**
     * @param $pattern
     * @return AST\Pattern
     */
    public function getAST($pattern)
    {
        $this->lexer->setInput($pattern);
        return $this->getPattern();
    }

    /**
     * Create AST root
     *
     * @return AST\Pattern
     */
    private function getPattern()
    {
        $this->lexer->moveNext();

        switch ($this->lexer->lookahead['type']) {
            case Lexer::T_TYPE_PATTERN:
                $pattern = new AST\Pattern(new AST\Type($this->lexer->lookahead['value']));
                break;
            default:
                $this->unexpectedSyntaxError($this->lexer->lookahead, "@type@ pattern");
                break;
        }

        $this->lexer->moveNext();

        if (!$this->endOfPattern()) {
            $this->addExpanderNodes($pattern);
        }

        return $pattern;
    }

    /**
     * @param AST\Pattern $pattern
     */
    private function addExpanderNodes(AST\Pattern $pattern)
    {
        while(($expander = $this->getNextExpanderNode()) !== null)  {
            $pattern->addExpander($expander);
        }
    }

    /**
     * Try to get next expander, return null if there is no expander left
     * @return AST\Expander|null
     */
    private function getNextExpanderNode()
    {
        if ($this->endOfPattern()) {
            return ;
        }

        $expander = new AST\Expander($this->getExpanderName());

        if ($this->endOfPattern()) {
            $this->unexpectedEndOfString(")");
        }

        $this->addArgumentValues($expander);

        if ($this->endOfPattern()) {
            $this->unexpectedEndOfString(")");
        }

        if (!$this->isNextCloseParenthesis()) {
            $this->unexpectedSyntaxError($this->lexer->lookahead, ")");
        }

        return $expander;
    }

    /**
     * @return mixed
     * @throws PatternException
     */
    private function getExpanderName()
    {
        if ($this->lexer->lookahead['type'] !== Lexer::T_EXPANDER_NAME) {
            $this->unexpectedSyntaxError($this->lexer->lookahead, ".expanderName(args) definition");
        }
        $expander = $this->lexer->lookahead['value'];
        $this->lexer->moveNext();

        return $expander;
    }

    /**
     * Add arguments to expander
     *
     * @param AST\Expander $expander
     */
    private function addArgumentValues(AST\Expander $expander)
    {
        while(($argument = $this->getNextArgumentValue()) !== null) {
            $argument = ($argument === self::NULL_VALUE) ? null : $argument;
            $expander->addArgument($argument);
            if (!$this->lexer->isNextToken(Lexer::T_COMMA)){
                break;
            }

            $this->lexer->moveNext();

            if ($this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS)) {
                $this->unexpectedSyntaxError($this->lexer->lookahead, "string, number, boolean or null argument");
            }
        }
    }

    /**
     * Try to get next argument. Return false if there are no arguments left before ")"
     * @return null|mixed
     */
    private function getNextArgumentValue()
    {
        $validArgumentTypes = array(
            Lexer::T_STRING,
            Lexer::T_NUMBER,
            Lexer::T_BOOLEAN,
            Lexer::T_NULL
        );

        if ($this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS)) {
            return ;
        }

        if ($this->lexer->isNextToken(Lexer::T_OPEN_CURLY_BRACE)) {
            return $this->getArrayArgument();
        }

        if ($this->lexer->isNextToken(Lexer::T_EXPANDER_NAME)) {
            return $this->getNextExpanderNode();
        }

        if (!$this->lexer->isNextTokenAny($validArgumentTypes)) {
            $this->unexpectedSyntaxError($this->lexer->lookahead, "string, number, boolean or null argument");
        }

        $tokenType = $this->lexer->lookahead['type'];
        $argument = $this->lexer->lookahead['value'];
        $this->lexer->moveNext();

        if ($tokenType === Lexer::T_NULL) {
            $argument = self::NULL_VALUE;
        }

        return $argument;
    }

    /**
     * return array
     */
    private function getArrayArgument()
    {
        $arrayArgument = array();
        $this->lexer->moveNext();

        while ($this->getNextArrayElement($arrayArgument) !== null) {
            $this->lexer->moveNext();
        }

        if (!$this->lexer->isNextToken(Lexer::T_CLOSE_CURLY_BRACE)) {
            $this->unexpectedSyntaxError($this->lexer->lookahead, "}");
        }

        $this->lexer->moveNext();

        return $arrayArgument;
    }

    /**
     * @param array $array
     * @return bool
     * @throws PatternException
     */
    private function getNextArrayElement(array &$array)
    {
        if ($this->lexer->isNextToken(Lexer::T_CLOSE_CURLY_BRACE)) {
            return ;
        }

        $key = $this->getNextArgumentValue();
        if ($key === self::NULL_VALUE) {
            $key = "";
        }

        if (!$this->lexer->isNextToken(Lexer::T_COLON)) {
            $this->unexpectedSyntaxError($this->lexer->lookahead, ":");
        }

        $this->lexer->moveNext();

        $value = $this->getNextArgumentValue();
        if ($value === self::NULL_VALUE) {
            $value = null;
        }

        $array[$key] = $value;

        if (!$this->lexer->isNextToken(Lexer::T_COMMA)) {
            return ;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isNextCloseParenthesis()
    {
        $isCloseParenthesis = $this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS);
        $this->lexer->moveNext();

        return $isCloseParenthesis;
    }

    /**
     * @param $unexpectedToken
     * @param null $expected
     * @throws PatternException
     */
    private function unexpectedSyntaxError($unexpectedToken, $expected = null)
    {
        $tokenPos = (isset($unexpectedToken['position'])) ? $unexpectedToken['position'] : '-1';
        $message  = sprintf("line 0, col %d: Error: ", $tokenPos);
        $message .= (isset($expected)) ? sprintf("Expected \"%s\", got ", $expected) : "Unexpected";
        $message .= sprintf("\"%s\"", $unexpectedToken['value']);

        throw PatternException::syntaxError($message);
    }

    /**
     * @param null $expected
     * @throws PatternException
     */
    private function unexpectedEndOfString($expected = null)
    {
        $tokenPos = (isset($this->lexer->token['position'])) ? $this->lexer->token['position'] + strlen($this->lexer->token['value']) : '-1';
        $message  = sprintf("line 0, col %d: Error: ", $tokenPos);
        $message .= (isset($expected)) ? sprintf("Expected \"%s\", got end of string.", $expected) : "Unexpected";
        $message .= "end of string";

        throw PatternException::syntaxError($message);
    }

    /**
     * @return bool
     */
    private function endOfPattern()
    {
        return is_null($this->lexer->lookahead);
    }
}

<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Exception\Exception;
use Coduo\PHPMatcher\Exception\PatternException;
use Coduo\PHPMatcher\Matcher\Pattern;
use Coduo\PHPMatcher\Parser\ExpanderInitializer;

final class Parser
{
    /**
     * @var string
     */
    public const NULL_VALUE = 'null';

    private Lexer $lexer;

    private ExpanderInitializer $expanderInitializer;

    public function __construct(Lexer $lexer, ExpanderInitializer $expanderInitializer)
    {
        $this->lexer = $lexer;
        $this->expanderInitializer = $expanderInitializer;
    }

    public function hasValidSyntax(string $pattern) : bool
    {
        try {
            $this->getAST($pattern);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function parse(string $pattern) : Pattern\TypePattern
    {
        $AST = $this->getAST($pattern);
        $pattern = new Pattern\TypePattern((string) $AST->getType());

        foreach ($AST->getExpanders() as $expander) {
            $pattern->addExpander($this->expanderInitializer->initialize($expander));
        }

        return $pattern;
    }

    public function getAST(string $pattern) : AST\Pattern
    {
        if ($pattern === '') {
            return new AST\Pattern(new AST\Type(''));
        }

        $this->lexer->setInput($pattern);

        return $this->getPattern();
    }

    private function getPattern() : AST\Pattern
    {
        $this->lexer->moveNext();

        $pattern = null;

        if ($this->lexer->lookahead['type'] == Lexer::T_TYPE_PATTERN) {
            $pattern = new AST\Pattern(new AST\Type($this->lexer->lookahead['value']));
        } else {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, '@type@ pattern'));
        }

        $this->lexer->moveNext();

        if (!$this->endOfPattern() && $pattern instanceof AST\Pattern) {
            $this->addExpanderNodes($pattern);
        }

        return $pattern;
    }

    private function addExpanderNodes(AST\Pattern $pattern) : void
    {
        while (($expander = $this->getNextExpanderNode()) !== null) {
            $pattern->addExpander($expander);
        }
    }

    /**
     * Try to get next expander, return null if there is no expander left.
     */
    private function getNextExpanderNode() : ?AST\Expander
    {
        if ($this->endOfPattern()) {
            return null;
        }

        $expander = new AST\Expander($this->getExpanderName());

        /* @phpstan-ignore-next-line  */
        if ($this->endOfPattern()) {
            $this->unexpectedEndOfString(')');
        }

        $this->addArgumentValues($expander);

        if ($this->endOfPattern()) {
            $this->unexpectedEndOfString(')');
        }

        if (!$this->isNextCloseParenthesis()) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, ')'));
        }

        return $expander;
    }

    private function getExpanderName() : string
    {
        if ($this->lexer->lookahead['type'] !== Lexer::T_EXPANDER_NAME) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, '.expanderName(args) definition'));
        }

        $expander = $this->lexer->lookahead['value'];

        if ($expander === null) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, '.expanderName(args) definition'));
        }

        $this->lexer->moveNext();

        if (\is_int($expander)) {
            return (string) $expander;
        }

        return $expander;
    }

    /**
     * Add arguments to expander.
     */
    private function addArgumentValues(AST\Expander $expander) : void
    {
        while (($argument = $this->getNextArgumentValue()) !== null) {
            $argument = ($argument === self::NULL_VALUE) ? null : $argument;
            $expander->addArgument($argument);

            if (!$this->lexer->isNextToken(Lexer::T_COMMA)) {
                break;
            }

            $this->lexer->moveNext();

            if ($this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS)) {
                throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, 'string, number, boolean or null argument'));
            }
        }
    }

    /**
     * Try to get next argument. Return false if there are no arguments left before ")".
     */
    private function getNextArgumentValue()
    {
        $validArgumentTypes = [
            Lexer::T_STRING,
            Lexer::T_NUMBER,
            Lexer::T_BOOLEAN,
            Lexer::T_NULL,
        ];

        if ($this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS)) {
            return;
        }

        if ($this->lexer->isNextToken(Lexer::T_OPEN_CURLY_BRACE)) {
            return $this->getArrayArgument();
        }

        if ($this->lexer->isNextToken(Lexer::T_EXPANDER_NAME)) {
            return $this->getNextExpanderNode();
        }

        if (!$this->lexer->isNextTokenAny($validArgumentTypes)) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, 'string, number, boolean or null argument'));
        }

        $tokenType = $this->lexer->lookahead['type'];
        $argument = $this->lexer->lookahead['value'];
        $this->lexer->moveNext();

        if ($tokenType === Lexer::T_NULL) {
            $argument = self::NULL_VALUE;
        }

        return $argument;
    }

    private function getArrayArgument() : array
    {
        $arrayArgument = [];
        $this->lexer->moveNext();

        while ($this->getNextArrayElement($arrayArgument) !== null) {
            $this->lexer->moveNext();
        }

        if (!$this->lexer->isNextToken(Lexer::T_CLOSE_CURLY_BRACE)) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, '}'));
        }

        $this->lexer->moveNext();

        return $arrayArgument;
    }

    private function getNextArrayElement(array &$array) : ?bool
    {
        if ($this->lexer->isNextToken(Lexer::T_CLOSE_CURLY_BRACE)) {
            return null;
        }

        $key = $this->getNextArgumentValue();

        if ($key === self::NULL_VALUE) {
            $key = '';
        }

        if (!$this->lexer->isNextToken(Lexer::T_COLON)) {
            throw PatternException::syntaxError($this->unexpectedSyntaxError($this->lexer->lookahead, ':'));
        }

        $this->lexer->moveNext();

        $value = $this->getNextArgumentValue();

        if ($value === self::NULL_VALUE) {
            $value = null;
        }

        $array[$key] = $value;

        if (!$this->lexer->isNextToken(Lexer::T_COMMA)) {
            return null;
        }

        return true;
    }

    private function isNextCloseParenthesis() : bool
    {
        $isCloseParenthesis = $this->lexer->isNextToken(Lexer::T_CLOSE_PARENTHESIS);
        $this->lexer->moveNext();

        return $isCloseParenthesis;
    }

    /**
     * @param mixed $unexpectedToken
     * @param string $expected
     */
    private function unexpectedSyntaxError($unexpectedToken, string $expected = null) : string
    {
        $tokenPos = (isset($unexpectedToken['position'])) ? $unexpectedToken['position'] : '-1';
        $message  = \sprintf('line 0, col %d: Error: ', $tokenPos);
        $message .= (isset($expected)) ? \sprintf('Expected "%s", got ', $expected) : 'Unexpected';
        $message .= \sprintf('"%s"', $unexpectedToken['value']);

        return $message;
    }

    /**
     * @param string $expected
     *
     * @throws PatternException
     */
    private function unexpectedEndOfString(string $expected = null) : void
    {
        $tokenPos = (isset($this->lexer->token['position'])) ? $this->lexer->token['position'] + \strlen((string) $this->lexer->token['value']) : '-1';
        $message  = \sprintf('line 0, col %d: Error: ', $tokenPos);
        $message .= (isset($expected)) ? \sprintf('Expected "%s", got end of string.', $expected) : 'Unexpected';
        $message .= 'end of string';

        throw PatternException::syntaxError($message);
    }

    private function endOfPattern() : bool
    {
        return null === $this->lexer->lookahead;
    }
}

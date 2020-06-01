<?php declare(strict_types=1);

namespace Mathias\ParserCombinator\Parser;

use Mathias\ParserCombinator\ParseResult\ParseResult;
use function Mathias\ParserCombinator\ParseResult\fail;
use function Mathias\ParserCombinator\ParseResult\succeed;

/**
 * @internal
 */
final class TakeWhile
{
    /**
     * Keep parsing 0 or more characters as long as the predicate holds.
     *
     * @template T
     *
     * @param callable(string) : bool $predicate
     *
     * @return Parser<T>
     */
    public static function _takeWhile(callable $predicate): Parser
    {
        /**
         * @see \Tests\Mathias\ParserCombinator\primitivesTest::not_sure_how_takeWhile_should_deal_with_EOF()
         */
        return new Parser(
            fn(string $input): ParseResult =>
                //self::isEOF($input) ?
                //    fail("takeWhile(predicate)", "EOF") :
                self::parseRemainingInput($input, $predicate)
        );
    }

    /**
     * Keep parsing 1 or more characters as long as the predicate holds.
     *
     * @template T
     *
     * @param callable(string) : bool $predicate
     *
     * @return Parser<T>
     */
    public static function _takeWhile1(callable $predicate): Parser
    {
        return new Parser(
            fn(string $input): ParseResult =>
                !self::matchFirst($predicate, $input) ?
                    fail("takeWhile1(predicate)", $input) :
                self::parseRemainingInput($input, $predicate)
        );
    }

    private static function isEOF(string $input): bool
    {
        return mb_strlen($input) === 0;
    }

    /**
     * @param callable(string) : bool $predicate
     */
    private static function parseRemainingInput(string $input, callable $predicate): ParseResult
    {
        $chunk = "";
        $remaining = $input;
        while (!self::isEOF($remaining) && self::matchFirst($predicate, $remaining)) {
            $chunk .= mb_substr($remaining, 0, 1);
            $remaining = mb_substr($remaining, 1);
        }
        return succeed($chunk, $remaining);
    }

    /**
     * @param callable(string) : bool $predicate
     */
    private static function matchFirst(callable $predicate, string $str): bool
    {
        return $predicate(mb_substr($str, 0, 1));
    }

}
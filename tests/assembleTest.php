<?php declare(strict_types=1);

namespace Tests\Mathias\ParserCombinator;

use Mathias\ParserCombinator\PHPUnit\ParserAssertions;
use PHPUnit\Framework\TestCase;
use function Mathias\ParserCombinator\assemble;
use function Mathias\ParserCombinator\char;
use function Mathias\ParserCombinator\string;

final class assembleTest extends TestCase
{
    use ParserAssertions;

    /** @test */
    public function assemble_string()
    {
        $parser = assemble(
            string('first'),
            string('second'),
        );
        $this->assertParse("firstsecond", $parser, "firstsecond");
        $this->assertRemain("", $parser, "firstsecond");
    }

    /** @test */
    public function assemble_string_ignore()
    {
        $parser = assemble(
            string('first')->thenIgnore(char('-')),
            string('second'),
        );
        $this->assertParse("firstsecond", $parser, "first-second");
        $this->assertRemain("", $parser, "first-second");
    }

    /** @test */
    public function assemble_arrays()
    {
        $toArray = fn($v) => [$v];

        $parser = assemble(
            string('first')->map($toArray),
            string('second')->map($toArray),
        );

        $input = "firstsecond";
        $expected = ["first", "second"];
        $this->assertParse($expected, $parser, $input);
    }

    /** @test */
    public function assemble_different_types_but_the_others_are_ignored()
    {
        // @todo this could be more elegant
        $toArray = fn($v) => [$v];
        $parser = assemble(
            char('[')->sequence(
                string('first')->map($toArray)
            ),
            char(',')->sequence(
                string('second')->map($toArray)
            )->thenIgnore(char(']')),
        );

        $input = "[first,second]";
        $expected = ["first", "second"];

        $this->assertParse($expected, $parser, $input);
    }
}
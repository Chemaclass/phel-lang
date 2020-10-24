<?php

declare(strict_types=1);

namespace PhelTest\Unit\Analyzer\TupleSymbol;

use Generator;
use Phel\Analyzer;
use Phel\Analyzer\TupleSymbol\IfSymbol;
use Phel\Ast\IfNode;
use Phel\Exceptions\PhelCodeException;
use Phel\GlobalEnvironment;
use Phel\Lang\Symbol;
use Phel\Lang\Tuple;
use Phel\NodeEnvironment;
use PHPUnit\Framework\TestCase;

final class IfSymbolTest extends TestCase
{
    /**
     * @dataProvider providerRequiresAtLeastTwoOrThreeArgs
     */
    public function testRequiresAtLeastTwoOrThreeArgs(Tuple $tuple): void
    {
        $this->expectException(PhelCodeException::class);
        $this->expectExceptionMessage("'if requires two or three arguments");

        $this->analyze($tuple);
    }

    public function providerRequiresAtLeastTwoOrThreeArgs(): Generator
    {
        yield 'No arguments provided: (if)' => [
            'tuple' => Tuple::create(
                Symbol::create(Symbol::NAME_IF)
            ),
        ];

        yield 'Only one argument provided: (if one)' => [
            'tuple' => Tuple::create(
                Symbol::create(Symbol::NAME_IF),
                Symbol::create('one'),
            ),
        ];

        yield 'Only one argument provided: (if one two three four)' => [
            'tuple' => Tuple::create(
                Symbol::create(Symbol::NAME_IF),
                Symbol::create('one'),
                Symbol::create('two'),
                Symbol::create('three'),
                Symbol::create('four'),
            ),
        ];
    }

    public function testAnalyze(): void
    {
        $tuple = Tuple::create(
            Symbol::create(Symbol::NAME_IF),
            true,
            'truthy',
            'falsy',
        );

        $actual = $this->analyze($tuple);

        self::assertTrue($actual->getTestExpr()->getValue());
        self::assertSame('truthy', $actual->getThenExpr()->getValue());
        self::assertSame('falsy', $actual->getElseExpr()->getValue());
        self::assertEquals(NodeEnvironment::empty(), $actual->getEnv());
    }

    private function analyze(Tuple $tuple): IfNode
    {
        $analyzer = new Analyzer(new GlobalEnvironment());

        return (new IfSymbol($analyzer))->analyze($tuple, NodeEnvironment::empty());
    }
}

<?php

declare(strict_types=1);

namespace Phel\Compiler\Analyzer;

use Phel\Compiler\Analyzer\TupleSymbol\ApplySymbol;
use Phel\Compiler\Analyzer\TupleSymbol\DefStructSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\DefSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\DoSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\FnSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\ForeachSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\IfSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\InvokeSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\LetSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\LoopSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\NsSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\PhpAGetSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\PhpAPushSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\PhpASetSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\PhpAUnsetSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\PhpNewSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\PhpObjectCallSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\QuoteSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\RecurSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\ThrowSymbol;
use Phel\Compiler\Analyzer\TupleSymbol\TrySymbol;
use Phel\Compiler\Analyzer\TupleSymbol\TupleSymbolAnalyzer;
use Phel\Compiler\Ast\Node;
use Phel\Compiler\NodeEnvironment;
use Phel\Exceptions\AnalyzerException;
use Phel\Exceptions\PhelCodeException;
use Phel\Lang\Symbol;
use Phel\Lang\Tuple;

final class AnalyzeTuple
{
    use WithAnalyzer;

    private const EMPTY_SYMBOL_NAME = '';

    /**
     * @throws AnalyzerException|PhelCodeException
     */
    public function analyze(Tuple $tuple, NodeEnvironment $env): Node
    {
        $symbolName = $this->getSymbolName($tuple);
        $symbol = $this->createSymbolAnalyzerByName($symbolName);

        return $symbol->analyze($tuple, $env);
    }

    private function getSymbolName(Tuple $tuple): string
    {
        return isset($tuple[0]) && $tuple[0] instanceof Symbol
            ? $tuple[0]->getFullName()
            : self::EMPTY_SYMBOL_NAME;
    }

    private function createSymbolAnalyzerByName(string $symbolName): TupleSymbolAnalyzer
    {
        switch ($symbolName) {
            case Symbol::NAME_DEF:
                return new DefSymbol($this->analyzer);
            case Symbol::NAME_NS:
                return new NsSymbol($this->analyzer);
            case Symbol::NAME_FN:
                return new FnSymbol($this->analyzer);
            case Symbol::NAME_QUOTE:
                return new QuoteSymbol();
            case Symbol::NAME_DO:
                return new DoSymbol($this->analyzer);
            case Symbol::NAME_IF:
                return new IfSymbol($this->analyzer);
            case Symbol::NAME_APPLY:
                return new ApplySymbol($this->analyzer);
            case Symbol::NAME_LET:
                return new LetSymbol($this->analyzer);
            case Symbol::NAME_PHP_NEW:
                return new PhpNewSymbol($this->analyzer);
            case Symbol::NAME_PHP_OBJECT_CALL:
                return new PhpObjectCallSymbol($this->analyzer, $isStatic = false);
            case Symbol::NAME_PHP_OBJECT_STATIC_CALL:
                return new PhpObjectCallSymbol($this->analyzer, $isStatic = true);
            case Symbol::NAME_PHP_ARRAY_GET:
                return new PhpAGetSymbol($this->analyzer);
            case Symbol::NAME_PHP_ARRAY_SET:
                return new PhpASetSymbol($this->analyzer);
            case Symbol::NAME_PHP_ARRAY_PUSH:
                return new PhpAPushSymbol($this->analyzer);
            case Symbol::NAME_PHP_ARRAY_UNSET:
                return new PhpAUnsetSymbol($this->analyzer);
            case Symbol::NAME_RECUR:
                return new RecurSymbol($this->analyzer);
            case Symbol::NAME_TRY:
                return new TrySymbol($this->analyzer);
            case Symbol::NAME_THROW:
                return new ThrowSymbol($this->analyzer);
            case Symbol::NAME_LOOP:
                return new LoopSymbol($this->analyzer);
            case Symbol::NAME_FOREACH:
                return new ForeachSymbol($this->analyzer);
            case Symbol::NAME_DEF_STRUCT:
                return new DefStructSymbol($this->analyzer);
            default:
                return new InvokeSymbol($this->analyzer);
        }
    }
}

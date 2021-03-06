<?php

declare(strict_types=1);

namespace Phel\Compiler\Analyzer\TupleSymbol;

use Phel\Compiler\Ast\QuoteNode;
use Phel\Compiler\NodeEnvironment;
use Phel\Exceptions\AnalyzerException;
use Phel\Lang\Symbol;
use Phel\Lang\Tuple;

final class QuoteSymbol implements TupleSymbolAnalyzer
{
    public function analyze(Tuple $tuple, NodeEnvironment $env): QuoteNode
    {
        if (!($tuple[0] instanceof Symbol && $tuple[0]->getName() === Symbol::NAME_QUOTE)) {
            throw AnalyzerException::withLocation("This is not a 'quote.", $tuple);
        }

        if (count($tuple) !== 2) {
            throw AnalyzerException::withLocation("Exactly one argument is required for 'quote", $tuple);
        }

        return new QuoteNode(
            $env,
            $tuple[1],
            $tuple->getStartLocation()
        );
    }
}

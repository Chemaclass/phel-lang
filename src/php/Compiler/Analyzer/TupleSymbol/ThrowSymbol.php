<?php

declare(strict_types=1);

namespace Phel\Compiler\Analyzer\TupleSymbol;

use Phel\Compiler\Analyzer\WithAnalyzer;
use Phel\Compiler\Ast\ThrowNode;
use Phel\Compiler\NodeEnvironment;
use Phel\Exceptions\AnalyzerException;
use Phel\Lang\Tuple;

final class ThrowSymbol implements TupleSymbolAnalyzer
{
    use WithAnalyzer;

    public function analyze(Tuple $tuple, NodeEnvironment $env): ThrowNode
    {
        if (count($tuple) !== 2) {
            throw AnalyzerException::withLocation("Exact one argument is required for 'throw", $tuple);
        }

        return new ThrowNode(
            $env,
            $this->analyzer->analyze($tuple[1], $env->withContext(NodeEnvironment::CONTEXT_EXPRESSION)->withDisallowRecurFrame()),
            $tuple->getStartLocation()
        );
    }
}

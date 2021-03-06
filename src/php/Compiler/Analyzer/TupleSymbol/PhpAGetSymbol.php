<?php

declare(strict_types=1);

namespace Phel\Compiler\Analyzer\TupleSymbol;

use Phel\Compiler\Analyzer\WithAnalyzer;
use Phel\Compiler\Ast\PhpArrayGetNode;
use Phel\Compiler\NodeEnvironment;
use Phel\Lang\Tuple;

final class PhpAGetSymbol implements TupleSymbolAnalyzer
{
    use WithAnalyzer;

    public function analyze(Tuple $tuple, NodeEnvironment $env): PhpArrayGetNode
    {
        return new PhpArrayGetNode(
            $env,
            $this->analyzer->analyze($tuple[1], $env->withContext(NodeEnvironment::CONTEXT_EXPRESSION)),
            $this->analyzer->analyze($tuple[2], $env->withContext(NodeEnvironment::CONTEXT_EXPRESSION)),
            $tuple->getStartLocation()
        );
    }
}

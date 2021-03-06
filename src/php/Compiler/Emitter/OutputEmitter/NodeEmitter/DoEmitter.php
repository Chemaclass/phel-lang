<?php

declare(strict_types=1);

namespace Phel\Compiler\Emitter\OutputEmitter\NodeEmitter;

use Phel\Compiler\Ast\DoNode;
use Phel\Compiler\Ast\Node;
use Phel\Compiler\Emitter\OutputEmitter\NodeEmitter;
use Phel\Compiler\NodeEnvironment;

final class DoEmitter implements NodeEmitter
{
    use WithOutputEmitter;

    public function emit(Node $node): void
    {
        assert($node instanceof DoNode);

        $wrapFn = count($node->getStmts()) > 0 && $node->getEnv()->getContext() === NodeEnvironment::CONTEXT_EXPRESSION;
        if ($wrapFn) {
            $this->outputEmitter->emitFnWrapPrefix($node->getEnv(), $node->getStartSourceLocation());
        }

        foreach ($node->getStmts() as $i => $stmt) {
            $this->outputEmitter->emitNode($stmt);
            $this->outputEmitter->emitLine();
        }
        $this->outputEmitter->emitNode($node->getRet());

        if ($wrapFn) {
            $this->outputEmitter->emitFnWrapSuffix($node->getStartSourceLocation());
        }
    }
}

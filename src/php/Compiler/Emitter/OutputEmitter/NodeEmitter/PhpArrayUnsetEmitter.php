<?php

declare(strict_types=1);

namespace Phel\Compiler\Emitter\OutputEmitter\NodeEmitter;

use Phel\Compiler\Ast\Node;
use Phel\Compiler\Ast\PhpArrayUnsetNode;
use Phel\Compiler\Emitter\OutputEmitter\NodeEmitter;

final class PhpArrayUnsetEmitter implements NodeEmitter
{
    use WithOutputEmitter;

    public function emit(Node $node): void
    {
        assert($node instanceof PhpArrayUnsetNode);

        $this->outputEmitter->emitContextPrefix($node->getEnv(), $node->getStartSourceLocation());
        $this->outputEmitter->emitStr('unset((', $node->getStartSourceLocation());
        $this->outputEmitter->emitNode($node->getArrayExpr());
        $this->outputEmitter->emitStr(')[(', $node->getStartSourceLocation());
        $this->outputEmitter->emitNode($node->getAccessExpr());
        $this->outputEmitter->emitStr(')])', $node->getStartSourceLocation());
        $this->outputEmitter->emitContextSuffix($node->getEnv(), $node->getStartSourceLocation());
    }
}

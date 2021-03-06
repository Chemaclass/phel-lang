<?php

declare(strict_types=1);

namespace Phel\Compiler\Ast;

use Phel\Compiler\NodeEnvironment;
use Phel\Lang\SourceLocation;

final class LetNode extends Node
{
    /** @var BindingNode[] */
    private array $bindings;

    private Node $bodyExpr;

    private bool $isLoop;

    /**
     * @param BindingNode[] $bindings
     */
    public function __construct(
        NodeEnvironment $env,
        array $bindings,
        Node $bodyExpr,
        bool $isLoop,
        ?SourceLocation $sourceLocation = null
    ) {
        parent::__construct($env, $sourceLocation);
        $this->bindings = $bindings;
        $this->bodyExpr = $bodyExpr;
        $this->isLoop = $isLoop;
    }

    /**
     * @return BindingNode[]
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    public function getBodyExpr(): Node
    {
        return $this->bodyExpr;
    }

    public function isLoop(): bool
    {
        return $this->isLoop;
    }
}

<?php

declare(strict_types=1);

namespace Phel\Compiler\Ast;

use Phel\Compiler\NodeEnvironment;
use Phel\Lang\SourceLocation;

final class DoNode extends Node
{
    /** @var Node[] */
    private array $stmts;

    private Node $ret;

    /**
     * @param Node[] $stmts
     */
    public function __construct(NodeEnvironment $env, array $stmts, Node $ret, ?SourceLocation $sourceLocation = null)
    {
        parent::__construct($env, $sourceLocation);
        $this->stmts = $stmts;
        $this->ret = $ret;
    }

    /**
     * @return Node[]
     */
    public function getStmts(): array
    {
        return $this->stmts;
    }

    public function getRet(): Node
    {
        return $this->ret;
    }
}

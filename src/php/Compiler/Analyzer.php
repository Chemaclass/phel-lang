<?php

declare(strict_types=1);

namespace Phel\Compiler;

use Phel\Compiler\Analyzer\AnalyzeArray;
use Phel\Compiler\Analyzer\AnalyzeBracketTuple;
use Phel\Compiler\Analyzer\AnalyzeLiteral;
use Phel\Compiler\Analyzer\AnalyzeSymbol;
use Phel\Compiler\Analyzer\AnalyzeTable;
use Phel\Compiler\Analyzer\AnalyzeTuple;
use Phel\Compiler\Ast\Node;
use Phel\Exceptions\AnalyzerException;
use Phel\Lang\AbstractType;
use Phel\Lang\Keyword;
use Phel\Lang\PhelArray;
use Phel\Lang\Symbol;
use Phel\Lang\Table;
use Phel\Lang\Tuple;

final class Analyzer implements AnalyzerInterface
{
    private GlobalEnvironmentInterface $globalEnvironment;

    public function __construct(GlobalEnvironmentInterface $globalEnvironment)
    {
        $this->globalEnvironment = $globalEnvironment;
    }

    public function resolve(Symbol $name, NodeEnvironment $env): ?Node
    {
        return $this->globalEnvironment->resolve($name, $env);
    }

    public function getNamespace(): string
    {
        return $this->globalEnvironment->getNs();
    }

    public function setNamespace(string $ns): void
    {
        $this->globalEnvironment->setNs($ns);
    }

    public function addUseAlias(string $ns, Symbol $alias, Symbol $nsSymbol): void
    {
        $this->globalEnvironment->addUseAlias($ns, $alias, $nsSymbol);
    }

    public function addRequireAlias(string $ns, Symbol $alias, Symbol $nsSymbol): void
    {
        $this->globalEnvironment->addRequireAlias($ns, $alias, $nsSymbol);
    }

    /**
     * @param Symbol[] $referSymbols
     */
    public function addRefers(string $ns, array $referSymbols, Symbol $nsSymbol): void
    {
        foreach ($referSymbols as $referFnName) {
            $this->globalEnvironment->addRefer($ns, $referFnName, $nsSymbol);
        }
    }

    public function addDefinition(string $ns, Symbol $symbol, Table $meta): void
    {
        $this->globalEnvironment->addDefinition($ns, $symbol, $meta);
    }

    /**
     * @param AbstractType|string|float|int|bool|null $x
     */
    public function analyzeMacro($x, NodeEnvironment $env): Node
    {
        $this->globalEnvironment->setAllowPrivateAccess(true);
        $result = $this->analyze($x, $env);
        $this->globalEnvironment->setAllowPrivateAccess(false);

        return $result;
    }

    /**
     * @param AbstractType|string|float|int|bool|null $x
     */
    public function analyze($x, NodeEnvironment $env): Node
    {
        if ($this->isLiteral($x)) {
            return (new AnalyzeLiteral())->analyze($x, $env);
        }

        if ($x instanceof Symbol) {
            return (new AnalyzeSymbol($this))->analyze($x, $env);
        }

        if ($x instanceof Tuple && $x->isUsingBracket()) {
            return (new AnalyzeBracketTuple($this))->analyze($x, $env);
        }

        if ($x instanceof PhelArray) {
            return (new AnalyzeArray($this))->analyze($x, $env);
        }

        if ($x instanceof Table) {
            return (new AnalyzeTable($this))->analyze($x, $env);
        }

        if ($x instanceof Tuple) {
            return (new AnalyzeTuple($this))->analyze($x, $env);
        }

        throw new AnalyzerException('Unhandled type: ' . var_export($x, true));
    }

    /**
     * @param AbstractType|string|float|int|bool|null $x
     */
    private function isLiteral($x): bool
    {
        return is_string($x)
            || is_float($x)
            || is_int($x)
            || is_bool($x)
            || $x === null
            || $x instanceof Keyword;
    }
}

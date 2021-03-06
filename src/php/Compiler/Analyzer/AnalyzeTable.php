<?php

declare(strict_types=1);

namespace Phel\Compiler\Analyzer;

use Phel\Compiler\Ast\TableNode;
use Phel\Compiler\NodeEnvironment;
use Phel\Lang\Table;

final class AnalyzeTable
{
    use WithAnalyzer;

    public function analyze(Table $table, NodeEnvironment $env): TableNode
    {
        $keyValues = [];
        $kvEnv = $env->withContext(NodeEnvironment::CONTEXT_EXPRESSION);

        foreach ($table as $key => $value) {
            $keyValues[] = $this->analyzer->analyze($key, $kvEnv);
            $keyValues[] = $this->analyzer->analyze($value, $kvEnv);
        }

        return new TableNode($env, $keyValues, $table->getStartLocation());
    }
}

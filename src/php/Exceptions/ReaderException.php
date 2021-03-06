<?php

declare(strict_types=1);

namespace Phel\Exceptions;

use Exception;
use Phel\Compiler\ReadModel\CodeSnippet;
use Phel\Lang\SourceLocation;

final class ReaderException extends PhelCodeException
{
    private CodeSnippet $codeSnippet;

    public function __construct(
        string $message,
        SourceLocation $startLocation,
        SourceLocation $endLocation,
        CodeSnippet $codeSnippet,
        ?Exception $nestedException = null
    ) {
        parent::__construct($message, $startLocation, $endLocation, $nestedException);
        $this->codeSnippet = $codeSnippet;
    }

    public function getCodeSnippet(): CodeSnippet
    {
        return $this->codeSnippet;
    }
}

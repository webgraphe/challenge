<?php

namespace Webgraphe\PredicateTree\Exceptions;

use Exception;
use Webgraphe\PredicateTree\Contracts\ContextContract;

class ContextException extends Exception
{
    private ?ContextContract $context;

    public static function missingArbiter(ContextContract $context): self
    {
        return (new static("Arbiter is missing"))->withContext($context);
    }

    public static function invalidContext(ContextContract $context, string $message = null): self
    {
        return (new static("Invalid context: $message"))->withContext($context);
    }

    private function withContext(ContextContract $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getContext(): ?ContextContract
    {
        return $this->context;
    }
}

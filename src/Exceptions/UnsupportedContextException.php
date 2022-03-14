<?php

namespace Webgraphe\RuleTree\Exceptions;

use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\RuleTreeException;

class UnsupportedContextException extends RuleTreeException
{
    private ?ContextContract $context;

    /**
     * @param ContextContract $context
     * @param string $message
     * @return static
     */
    public static function create(ContextContract $context, string $message = ''): self
    {
        $instance = new static($message);
        $instance->context = $context;

        return $instance;
    }

    public function getContext(): ?ContextContract
    {
        return $this->context;
    }
}

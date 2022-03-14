<?php

namespace Webgraphe\PredicateTree\Exceptions;

use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\PredicateTreeException;

class UnsupportedContextException extends PredicateTreeException
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

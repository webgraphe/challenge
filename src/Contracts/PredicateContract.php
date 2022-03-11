<?php

namespace Webgraphe\PredicateTree\Contracts;

use Webgraphe\PredicateTree\Exceptions\ContextException;

interface PredicateContract
{
    /**
     * @param ContextContract $context
     * @return bool
     * @throws ContextException
     */
    public function evaluate(ContextContract $context): bool;

    public function hash(): string;
}

<?php

namespace Webgraphe\PredicateTree\Contracts;

use Webgraphe\PredicateTree\Exceptions\UnsupportedContextException;

interface RuleContract
{
    /**
     * @param ContextContract $context
     * @return bool
     * @throws UnsupportedContextException
     */
    public function evaluate(ContextContract $context): bool;

    public function hash(ContextContract $context): string;

    public function toArray(ContextContract $context): array;
}

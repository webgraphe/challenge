<?php

namespace Webgraphe\PredicateTree\Contracts;

interface RuleContract
{
    /**
     * @param ContextContract $context
     * @return bool
     */
    public function evaluate(ContextContract $context): bool;

    public function hash(ContextContract $context): string;

    public function toArray(ContextContract $context): array;
}

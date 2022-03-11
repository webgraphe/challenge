<?php

namespace Webgraphe\PredicateTree\Contracts;

interface ArbiterContract
{
    public function evaluate(PredicateContract $predicate): bool;

    public function getContext(): ContextContract;
}

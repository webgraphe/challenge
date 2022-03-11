<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Contracts\ArbiterContract;
use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\PredicateContract;

class Arbiter implements ArbiterContract
{
    private ContextContract $context;
    /** @var array<string, bool|null> */
    private array $cache = [];

    public function __construct(ContextContract $context)
    {
        $this->context = $context->withArbiter($this);
    }

    /**
     * @param PredicateContract $predicate
     * @return bool
     * @throws Exceptions\ContextException
     */
    public function evaluate(PredicateContract $predicate): bool
    {
        return $this->cache[$predicate->hash()] ??= $predicate->evaluate($this->context);
    }

    public function getContext(): ContextContract
    {
        return $this->context;
    }
}

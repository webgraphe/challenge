<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\PredicateContract;
use Webgraphe\PredicateTree\Exceptions\ContextException;

class Context implements ContextContract
{
    private ?Arbiter $arbiter;

    public function jsonSerialize(): array
    {
        // TODO Implement stub
        return [];
    }

    public function getArbiter(): Arbiter
    {
        return $this->arbiter;
    }

    public function withArbiter(Arbiter $arbiter): self
    {
        $this->arbiter = $arbiter;

        return $this;
    }

    /**
     * @param PredicateContract $predicate
     * @return bool
     * @throws ContextException
     */
    public function evaluate(PredicateContract $predicate): bool
    {
        if (!$this->arbiter) {
            throw ContextException::missingArbiter($this);
        }

        return $this->arbiter->evaluate($predicate);
    }
}

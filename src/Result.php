<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\RuleContract;

class Result
{
    private RuleContract $predicate;
    private bool $success;

    /**
     * @param RuleContract $predicate
     * @param bool $success
     */
    public function __construct(RuleContract $predicate, bool $success)
    {
        $this->predicate = $predicate;
        $this->success = $success;
    }

    public function toArray(ContextContract $context): array
    {
        return [
            'rule' => $this->predicate->toArray($context),
            'success' => $this->success,
        ];
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }
}

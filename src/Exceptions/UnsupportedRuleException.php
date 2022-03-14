<?php

namespace Webgraphe\PredicateTree\Exceptions;

use Webgraphe\PredicateTree\Contracts\RuleContract;
use Webgraphe\PredicateTree\PredicateTreeException;

class UnsupportedRuleException extends PredicateTreeException
{
    private ?RuleContract $rule;

    /**
     * @param RuleContract $rule
     * @param string $message
     * @return static
     */
    public static function create(RuleContract $rule, string $message = ''): self
    {
        $instance = new static($message);
        $instance->rule = $rule;

        return $instance;
    }

    public function getRule(): ?RuleContract
    {
        return $this->rule;
    }
}

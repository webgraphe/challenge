<?php

namespace Webgraphe\RuleTree\Exceptions;

use Webgraphe\RuleTree\Contracts\RuleContract;
use Webgraphe\RuleTree\RuleTreeException;

class UnsupportedRuleException extends RuleTreeException
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

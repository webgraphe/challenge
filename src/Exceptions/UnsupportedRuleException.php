<?php

namespace Webgraphe\Challenge\Exceptions;

use Webgraphe\Challenge\Contracts\RuleContract;
use Webgraphe\Challenge\RuleTreeException;

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

<?php

namespace Webgraphe\RuleTree;

use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\Contracts\RuleContract;

class Result
{
    private RuleContract $rule;
    private bool $success;

    /**
     * @param RuleContract $rule
     * @param bool $success
     */
    public function __construct(RuleContract $rule, bool $success)
    {
        $this->rule = $rule;
        $this->success = $success;
    }

    public function toArray(ContextContract $context): array
    {
        return [
            'rule' => $this->rule->toArray($context),
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

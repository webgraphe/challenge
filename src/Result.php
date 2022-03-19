<?php

namespace Webgraphe\Challenge;

use Webgraphe\Challenge\Contracts\ContextContract;
use Webgraphe\Challenge\Contracts\RuleContract;

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

    public function marshal(ContextContract $context): array
    {
        return [
            'rule' => $this->rule->marshal($context),
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

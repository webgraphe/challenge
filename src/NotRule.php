<?php

namespace Webgraphe\RuleTree;

use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\Exceptions\RuleEvaluationException;

class NotRule extends AbstractRule
{
    private AbstractRule $rule;

    final public function __construct(AbstractRule $rule)
    {
        parent::__construct();
        $this->rule = $rule;
    }

    public function summary(): string
    {
        return "Negates the operand";
    }

    public function description(): string
    {
        return "Returns TRUE if and only if the operand returns FALSE";
    }

    public function toArray(ContextContract $context): array
    {
        return array_merge(
            parent::toArray($context),
            [
                'rule' => $this->rule->hash($context),
            ]
        );
    }

    /**
     * @param AbstractRule $rule
     * @return static
     */
    final public static function create(AbstractRule $rule): self
    {
        return new static($rule);
    }

    /**
     * @param Context $context
     * @return bool
     * @throws RuleEvaluationException
     */
    protected function evaluateProtected(Context $context): bool
    {
        return !$context->evaluate($this->rule);
    }
}

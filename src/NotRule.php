<?php

namespace Webgraphe\RuleTree;

use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\Exceptions\EvaluationException;

class NotRule extends AbstractRule
{
    private AbstractRule $rule;

    final public function __construct(AbstractRule $rule)
    {
        parent::__construct();
        $this->rule = $rule;
    }

    /**
     * @param AbstractRule $rule
     * @return static
     */
    final public static function create(AbstractRule $rule): self
    {
        return new static($rule);
    }

    public function summary(): string
    {
        return "Negates the operand";
    }

    public function description(): string
    {
        return "Returns TRUE if and only if the operand returns FALSE";
    }

    protected function data(ContextContract $context): array
    {
        return array_merge(
            parent::data($context),
            [
                'rule' => $this->rule->hash($context),
            ]
        );
    }

    /**
     * @return AbstractRule
     */
    public function getRule(): AbstractRule
    {
        return $this->rule;
    }

    /**
     * @param Context $context
     * @return bool
     * @throws EvaluationException
     */
    protected function evaluateProtected(Context $context): bool
    {
        return !$context->evaluate($this->rule);
    }
}

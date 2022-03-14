<?php

namespace Webgraphe\RuleTree;

use Webgraphe\RuleTree\Exceptions\RuleEvaluationException;

class AndRule extends AbstractListRule
{

    public function summary(): string
    {
        return "Logical AND";
    }

    public function description(): string
    {
        return "Returns TRUE if and only if all operands return TRUE";
    }

    /**
     * @param Context $context
     * @return bool
     * @throws RuleEvaluationException
     */
    protected function evaluateProtected(Context $context): bool
    {
        foreach ($this as $rule) {
            if (!$context->evaluate($rule)) {
                return false;
            }
        }

        return true;
    }
}

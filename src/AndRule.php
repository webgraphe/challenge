<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Exceptions\RuleException;

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
     * @throws RuleException
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

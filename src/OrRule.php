<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Exceptions\RuleException;

class OrRule extends AbstractListRule
{
    public function summary(): string
    {
        return "Logical OR";
    }

    public function description(): string
    {
        return "Returns TRUE if and only if at least one operand returns TRUE";
    }

    /**
     * @param Context $context
     * @return bool
     * @throws RuleException
     */
    protected function evaluateProtected(Context $context): bool
    {
        foreach ($this as $rule) {
            if ($context->evaluate($rule)) {
                return true;
            }
        }

        return false;
    }
}

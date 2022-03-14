<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Exceptions\RuleException;

class AndRule extends AbstractListRule
{
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

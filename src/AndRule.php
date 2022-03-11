<?php

namespace Webgraphe\PredicateTree;

class AndRule extends AbstractListRule
{
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

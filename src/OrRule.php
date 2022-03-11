<?php

namespace Webgraphe\PredicateTree;

class OrRule extends AbstractListRule
{
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

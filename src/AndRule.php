<?php

namespace Webgraphe\Challenge;

use Webgraphe\Challenge\Exceptions\EvaluationException;

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
     * @throws EvaluationException
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

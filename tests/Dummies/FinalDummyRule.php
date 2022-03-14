<?php

namespace Webgraphe\Tests\RuleTree\Dummies;

use Webgraphe\RuleTree\AbstractRule;
use Webgraphe\RuleTree\Context;

class FinalDummyRule extends AbstractRule
{
    protected function evaluateProtected(Context $context): bool
    {
        return true;
    }
}

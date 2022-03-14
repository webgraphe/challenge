<?php

namespace Webgraphe\Tests\PredicateTree\Dummies;

use Webgraphe\PredicateTree\AbstractRule;
use Webgraphe\PredicateTree\Context;

class FinalDummyRule extends AbstractRule
{
    protected function evaluateProtected(Context $context): bool
    {
        return true;
    }
}

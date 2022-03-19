<?php

namespace Webgraphe\Tests\Challenge\Dummies;

use Webgraphe\Challenge\AbstractRule;
use Webgraphe\Challenge\Context;

class FinalDummyRule extends AbstractRule
{
    protected function evaluateProtected(Context $context): bool
    {
        return true;
    }
}

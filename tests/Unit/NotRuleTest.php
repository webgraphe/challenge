<?php

namespace Webgraphe\Tests\RuleTree\Unit;

use Webgraphe\RuleTree\NotRule;
use Webgraphe\Tests\RuleTree\TestCase;

class NotRuleTest extends TestCase
{
    public function testGetRule()
    {
        $not = NotRule::create($rule = $this->booleanRule(true));
        $this->assertEquals($rule, $not->getRule());
    }
}

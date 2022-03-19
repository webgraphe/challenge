<?php

namespace Webgraphe\Tests\Challenge\Unit;

use Webgraphe\Challenge\NotRule;
use Webgraphe\Tests\Challenge\TestCase;

/**
 * @covers ::NotRule
 */
class NotRuleTest extends TestCase
{
    public function testGetRule()
    {
        $not = NotRule::create($rule = $this->booleanRule(true));
        $this->assertEquals($rule, $not->getRule());
    }
}

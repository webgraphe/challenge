<?php

namespace Webgraphe\Tests\LogicTree\Unit;

use Webgraphe\PredicateTree\AbstractRule;
use Webgraphe\PredicateTree\AndRule;
use Webgraphe\PredicateTree\Arbiter;
use Webgraphe\PredicateTree\Context;
use Webgraphe\PredicateTree\Exceptions\ContextException;
use Webgraphe\PredicateTree\NotRule;
use Webgraphe\PredicateTree\OrRule;
use Webgraphe\Tests\LogicTree\TestCase;

class ArbiterTest extends TestCase
{
    private function easyRule($returnValue): AbstractRule
    {
        return new class($returnValue) extends AbstractRule {
            private bool $returnValue;

            public function __construct(bool $returnValue)
            {
                parent::__construct();
                $this->returnValue = $returnValue;
            }

            protected function evaluateProtected(Context $context): bool
            {
                return $this->returnValue;
            }

            public function hash(): string
            {
                return md5(parent::hash() . ':' . ($this->returnValue ? '1' : '0'));
            }
        };
    }

    public function testConstruction(): Arbiter
    {
        $arbiter = new Arbiter($context = new Context());
        $context->withArbiter($arbiter);

        $this->assertEquals($context, $arbiter->getContext());

        return $arbiter;
    }

    /**
     * @depends testConstruction
     * @param Arbiter $arbiter
     * @return void
     * @throws ContextException
     */
    public function testEvaluateEasyRule(Arbiter $arbiter)
    {
        $this->assertTrue($arbiter->evaluate($this->easyRule(true)));
        $this->assertFalse($arbiter->evaluate($this->easyRule(false)));
    }

    /**
     * @depends testConstruction
     * @param Arbiter $arbiter
     * @return void
     * @throws ContextException
     */
    public function testEvaluateNot(Arbiter $arbiter)
    {
        $this->assertFalse($arbiter->evaluate(NotRule::create($this->easyRule(true))));
        $this->assertTrue($arbiter->evaluate(NotRule::create($this->easyRule(false))));
    }

    /**
     * @depends testConstruction
     * @param Arbiter $arbiter
     * @return void
     * @throws ContextException
     */
    public function testEvaluateOr(Arbiter $arbiter)
    {
        $this->assertFalse($arbiter->evaluate(OrRule::create($false = $this->easyRule(false))));
        $this->assertTrue($arbiter->evaluate(OrRule::create($true = $this->easyRule(true))));
        $this->assertFalse($arbiter->evaluate(OrRule::create($false, $false)));
        $this->assertTrue($arbiter->evaluate(OrRule::create($false, $true)));
        $this->assertTrue($arbiter->evaluate(OrRule::create($true, $true)));
        $this->assertTrue($arbiter->evaluate(OrRule::create($true, $false)));
    }

    /**
     * @depends testConstruction
     * @param Arbiter $arbiter
     * @return void
     * @throws ContextException
     */
    public function testEvaluateAnd(Arbiter $arbiter)
    {
        $this->assertFalse($arbiter->evaluate(AndRule::create($false = $this->easyRule(false))));
        $this->assertTrue($arbiter->evaluate(AndRule::create($true = $this->easyRule(true))));
        $this->assertFalse($arbiter->evaluate(AndRule::create($false, $false)));
        $this->assertFalse($arbiter->evaluate(AndRule::create($false, $true)));
        $this->assertTrue($arbiter->evaluate(AndRule::create($true, $true)));
        $this->assertFalse($arbiter->evaluate(AndRule::create($true, $false)));
    }
}

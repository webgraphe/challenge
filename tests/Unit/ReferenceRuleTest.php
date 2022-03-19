<?php

namespace Webgraphe\Tests\RuleTree\Unit;

use Webgraphe\RuleTree\Context;
use Webgraphe\RuleTree\Exceptions\EvaluationException;
use Webgraphe\RuleTree\Exceptions\InvalidRuleNameException;
use Webgraphe\RuleTree\Exceptions\InvalidSerializerException;
use Webgraphe\RuleTree\Exceptions\RuleNameConflictException;
use Webgraphe\RuleTree\Exceptions\RuleNotFoundException;
use Webgraphe\RuleTree\NotRule;
use Webgraphe\RuleTree\ReferenceRule;
use Webgraphe\Tests\RuleTree\TestCase;

class ReferenceRuleTest extends TestCase
{
    /**
     * @return Context
     * @throws InvalidSerializerException
     * @throws EvaluationException
     * @throws InvalidRuleNameException
     * @throws RuleNameConflictException
     */
    public function testReferenceRule(): Context
    {
        $context = Context::create();
        $name = 'yes';
        $context->registerRule($this->booleanRule(true), $name);
        $no = NotRule::create($reference = ReferenceRule::create($name));

        $this->assertEquals($name, $reference->getReference());
        $this->assertFalse($context->evaluate($no));

        return $context;
    }

    /**
     * @depends testReferenceRule
     * @param Context $context
     * @return void
     * @throws InvalidRuleNameException
     */
    public function testRuleNameConflictException(Context $context)
    {
        $this->expectException(RuleNameConflictException::class);

        $context->registerRule($this->booleanRule(true), 'yes');
    }

    /**
     * @return void
     * @throws EvaluationException
     * @throws InvalidSerializerException
     */
    public function testRuleNotFound()
    {
        $this->expectException(EvaluationException::class);

        Context::create()->evaluate(ReferenceRule::create('undefined'));
    }
}

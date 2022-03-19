<?php

namespace Webgraphe\Tests\Challenge\Unit;

use Webgraphe\Challenge\Context;
use Webgraphe\Challenge\Exceptions\EvaluationException;
use Webgraphe\Challenge\Exceptions\InvalidRuleNameException;
use Webgraphe\Challenge\Exceptions\InvalidSerializerException;
use Webgraphe\Challenge\Exceptions\RuleNameConflictException;
use Webgraphe\Challenge\NotRule;
use Webgraphe\Challenge\ReferenceRule;
use Webgraphe\Tests\Challenge\TestCase;

/**
 * @covers ReferenceRule
 */
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

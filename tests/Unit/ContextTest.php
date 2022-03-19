<?php

namespace Webgraphe\Tests\RuleTree\Unit;

use Exception;
use Webgraphe\RuleTree\AndRule;
use Webgraphe\RuleTree\Context;
use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\Exceptions\EvaluationException;
use Webgraphe\RuleTree\Exceptions\InvalidRuleNameException;
use Webgraphe\RuleTree\Exceptions\InvalidSerializerException;
use Webgraphe\RuleTree\Exceptions\RuleNameConflictException;
use Webgraphe\RuleTree\Exceptions\UnsupportedContextException;
use Webgraphe\RuleTree\NotRule;
use Webgraphe\RuleTree\OrRule;
use Webgraphe\RuleTree\ReferenceRule;
use Webgraphe\Tests\RuleTree\TestCase;

/**
 * @covers ::Context
 */
class ContextTest extends TestCase
{
    /**
     * @throws InvalidSerializerException
     */
    public function testConstruct()
    {
        $context = Context::create();
        $payload = [
            'class' => get_class($context),
            'serializer' => $context->getSerializer(),
            'resultCache' => [],
            'ruleStack' => [],
        ];
        $this->assertEquals(json_encode($payload), json_encode($context));
    }

    /**
     * @throws EvaluationException
     * @throws InvalidRuleNameException
     * @throws InvalidSerializerException
     * @throws RuleNameConflictException
     */
    public function testEvaluation()
    {
        $context = Context::create();
        $context->registerRule($true = $this->booleanRule(true), 'true');
        $false = $this->booleanRule(false);
        $this->assertFalse(
            $context->evaluate(
                $and = AndRule::create(
                    $subAnd = AndRule::create(
                        $ref = ReferenceRule::create('true'),
                        $orTrue = OrRule::create($true, $true)
                    ),
                    $orFalse = OrRule::create($not = NotRule::create($true), $false)
                )
            )
        );
        $this->assertEmpty($context->getRuleStack());
        $payload = [
            'class' => get_class($context),
            'serializer' => $context->getSerializer(),
            'resultCache' => [
                $true->hash($context) => [
                    'rule' => $true->marshal($context),
                    'success' => true,
                ],
                $ref->hash($context) => [
                    'rule' => $ref->marshal($context),
                    'success' => true,
                ],
                $orTrue->hash($context) => [
                    'rule' => $orTrue->marshal($context),
                    'success' => true,
                ],
                $subAnd->hash($context) => [
                    'rule' => $subAnd->marshal($context),
                    'success' => true,
                ],
                $not->hash($context) => [
                    'rule' => $not->marshal($context),
                    'success' => false,
                ],
                $false->hash($context) => [
                    'rule' => $false->marshal($context),
                    'success' => false,
                ],
                $orFalse->hash($context) => [
                    'rule' => $orFalse->marshal($context),
                    'success' => false,
                ],
                $and->hash($context) => [
                    'rule' => $and->marshal($context),
                    'success' => false,
                ],
            ],
            'ruleStack' => [],
        ];
        $this->assertEquals(json_encode($payload), json_encode($context));
    }

    /**
     * @throws InvalidSerializerException
     * @throws EvaluationException
     */
    public function testEvaluationException()
    {
        $context = Context::create();
        $this->expectException(EvaluationException::class);
        $this->expectExceptionMessage("Evaluation failed");

        $or = OrRule::create(
            $not = NotRule::create(
                $rule = $this->exceptionRule($message = "Failure is part of the success", $code = 123)
            )
        );
        try {
            $context->evaluate($or);
        } catch (EvaluationException $e) {
            $this->assertInstanceOf(Exception::class, $previous = $e->getPrevious());
            $this->assertEquals($message, $previous->getMessage());
            $this->assertEquals($code, $previous->getCode());
            $payload = [
                'class' => get_class($context),
                'serializer' => $context->getSerializer(),
                'resultCache' => [],
                'ruleStack' => [
                    $or->marshal($context),
                    $not->marshal($context),
                    $rule->marshal($context),
                ],
            ];
            $this->assertEquals(json_encode($payload), json_encode($context));

            throw $e;
        }
    }

    /**
     * @throws InvalidSerializerException
     */
    public function testSerializers()
    {
        foreach (Context::SERIALIZERS as $serializer) {
            $context = Context::create($serializer);
            $this->assertEquals($serializer, $context->getSerializer());
        }
    }

    public function testInvalidSerializer()
    {
        $this->assertFalse(function_exists($invalid = 'invalid'));

        $this->expectException(InvalidSerializerException::class);
        $this->expectExceptionMessage("'$invalid' does not exist");
        Context::create($invalid);
    }

    /**
     * @return void
     * @throws InvalidSerializerException
     * @throws InvalidRuleNameException
     * @throws RuleNameConflictException
     */
    public function testRegistry()
    {
        $context = Context::create();
        $context->registerRule($true = $this->booleanRule(true), 'true');
        $context->registerRule($false = $this->booleanRule(false), 'false');

        $this->assertNull($context->getRule('null'));
        $this->assertEquals($true, $context->getRule('true'));
        $this->assertEquals($false, $context->getRule('false'));
    }

    /**
     * @return void
     * @throws InvalidRuleNameException
     * @throws InvalidSerializerException
     * @throws RuleNameConflictException
     */
    public function testInvalidRuleNameException()
    {
        $context = Context::create();

        $this->expectException(InvalidRuleNameException::class);
        $name = '1nvalid';
        $this->expectExceptionMessage($name);
        $context->registerRule($this->booleanRule(true), $name);
    }

    /**
     * @return void
     * @throws InvalidRuleNameException
     * @throws InvalidSerializerException
     * @throws RuleNameConflictException
     */
    public function testRuleNameConflictException()
    {
        $context = Context::create();
        $context->registerRule($true = $this->booleanRule(true), $name = 'true');

        $this->expectException(RuleNameConflictException::class);
        $this->expectExceptionMessage($name);
        $context->registerRule($true, $name);
    }

    /**
     * @return void
     * @throws InvalidSerializerException
     * @throws UnsupportedContextException
     */
    public function testAssertType()
    {
        $context = Context::create();
        $this->assertInstanceOf(Context::class, Context::assertType($context));
    }

    public function testUnsupportedContextException()
    {
        $this->expectException(UnsupportedContextException::class);

        $context = new class implements ContextContract {
            public function serialize($value): string
            {
                return '';
            }

            public function toArray(): array
            {
                return [];
            }

            public function jsonSerialize(): array
            {
                return [];
            }
        };

        try {
            Context::assertType($context);
        } catch (UnsupportedContextException $e) {
            $this->assertEquals($context, $e->getContext());

            throw $e;
        }
    }
}

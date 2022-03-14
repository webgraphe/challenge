<?php

namespace Webgraphe\Tests\RuleTree\Unit;

use Exception;
use Webgraphe\RuleTree\AbstractRule;
use Webgraphe\RuleTree\AndRule;
use Webgraphe\RuleTree\Context;
use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\Exceptions\InvalidRuleNameException;
use Webgraphe\RuleTree\Exceptions\InvalidSerializerException;
use Webgraphe\RuleTree\Exceptions\RuleEvaluationException;
use Webgraphe\RuleTree\Exceptions\RuleNameConflictException;
use Webgraphe\RuleTree\Exceptions\UnsupportedContextException;
use Webgraphe\RuleTree\NotRule;
use Webgraphe\RuleTree\OrRule;
use Webgraphe\Tests\RuleTree\Dummies\AbstractDummyRule;
use Webgraphe\Tests\RuleTree\TestCase;

/**
 * @covers ::Context
 */
class ContextTest extends TestCase
{
    private function easyRule(bool $returnValue): AbstractDummyRule
    {
        return new class($returnValue) extends AbstractDummyRule {
            private bool $returnValue;

            public function __construct(bool $returnValue)
            {
                parent::__construct();
                $this->returnValue = $returnValue;
            }

            public function toArray(ContextContract $context): array
            {
                return array_merge(
                    parent::toArray($context),
                    [
                        'returnValue' => $this->returnValue,
                    ]
                );
            }

            protected function evaluateProtected(Context $context): bool
            {
                return $this->returnValue;
            }
        };
    }

    private function exceptionRule(string $message, int $code): AbstractRule
    {
        return new class($message, $code) extends AbstractDummyRule {
            private string $message;
            private int $code;

            public function __construct(string $message, int $code)
            {
                parent::__construct();
                $this->message = $message;
                $this->code = $code;
            }

            public function toArray(ContextContract $context): array
            {
                return array_merge(
                    parent::toArray($context),
                    [
                        'message' => $this->message,
                        'code' => $this->code,
                    ]
                );
            }

            protected function evaluateProtected(Context $context): bool
            {
                throw new Exception($this->message, $this->code);
            }
        };
    }

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
     * @throws InvalidSerializerException
     * @throws RuleEvaluationException
     */
    public function testEvaluation()
    {
        $context = Context::create();
        $true = $this->easyRule(true);
        $false = $this->easyRule(false);
        $this->assertFalse(
            $context->evaluate(
                $and = AndRule::create(
                    $subAnd = AndRule::create($true, $orTrue = OrRule::create($true, $true)),
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
                    'rule' => $true->toArray($context),
                    'success' => true,
                ],
                $orTrue->hash($context) => [
                    'rule' => $orTrue->toArray($context),
                    'success' => true,
                ],
                $subAnd->hash($context) => [
                    'rule' => $subAnd->toArray($context),
                    'success' => true,
                ],
                $not->hash($context) => [
                    'rule' => $not->toArray($context),
                    'success' => false,
                ],
                $false->hash($context) => [
                    'rule' => $false->toArray($context),
                    'success' => false,
                ],
                $orFalse->hash($context) => [
                    'rule' => $orFalse->toArray($context),
                    'success' => false,
                ],
                $and->hash($context) => [
                    'rule' => $and->toArray($context),
                    'success' => false,
                ],
            ],
            'ruleStack' => [],
        ];
        $this->assertEquals(json_encode($payload), json_encode($context));
    }

    /**
     * @throws InvalidSerializerException
     * @throws RuleEvaluationException
     */
    public function testEvaluationException()
    {
        $context = Context::create();
        $this->expectException(RuleEvaluationException::class);
        $this->expectExceptionMessage("Evaluation failed");

        $rule = $this->exceptionRule($message = "Failure is part of the success", $code = 123);
        try {
            $context->evaluate($rule);
        } catch (RuleEvaluationException $e) {
            $this->assertInstanceOf(Exception::class, $previous = $e->getPrevious());
            $this->assertEquals($message, $previous->getMessage());
            $this->assertEquals($code, $previous->getCode());
            $payload = [
                'class' => get_class($context),
                'serializer' => $context->getSerializer(),
                'resultCache' => [],
                'ruleStack' => [
                    $rule->toArray($context),
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
        $context->registerRule($true = $this->easyRule(true), 'true');
        $context->registerRule($false = $this->easyRule(false), 'false');

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
        $context->registerRule($this->easyRule(true), $name);
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
        $context->registerRule($true = $this->easyRule(true), $name = 'true');

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

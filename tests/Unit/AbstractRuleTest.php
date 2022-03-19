<?php

namespace Webgraphe\Tests\RuleTree\Unit;

use Webgraphe\RuleTree\AbstractRule;
use Webgraphe\RuleTree\Context;
use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\Contracts\RuleContract;
use Webgraphe\RuleTree\Exceptions\UnsupportedRuleException;
use Webgraphe\Tests\RuleTree\Dummies\AbstractDummyRule;
use Webgraphe\Tests\RuleTree\Dummies\FinalDummyRule;
use Webgraphe\Tests\RuleTree\TestCase;

class AbstractRuleTest extends TestCase
{
    private function rule(): AbstractRule
    {
        return new class() extends AbstractDummyRule {
            protected function evaluateProtected(Context $context): bool
            {
                return true;
            }
        };
    }

    /**
     * @throws UnsupportedRuleException
     */
    public function testAssertType()
    {
        $this->assertInstanceOf(AbstractRule::class, AbstractRule::assertType($this->rule()));
    }

    public function testSummaryAndDescription()
    {
        $rule = $this->rule();
        $this->assertEquals(AbstractDummyRule::NAME, $rule->name());
        $this->assertEquals(AbstractDummyRule::SUMMARY, $rule->summary());
        $this->assertEquals(AbstractDummyRule::DESCRIPTION, $rule->description());
    }

    public function testUnsupportedRuleException()
    {
        $this->expectException(UnsupportedRuleException::class);

        $rule = new class implements RuleContract {
            public function evaluate(ContextContract $context): bool
            {
                return false;
            }

            public function hash(ContextContract $context): string
            {
                return '';
            }

            public function summary(): string
            {
                return 'Summary';
            }

            public function description(): string
            {
                return 'Description';
            }

            public function name(): string
            {
                return 'Name';
            }

            public function marshal(ContextContract $context): array
            {
                return [];
            }
        };

        try {
            AbstractRule::assertType($rule);
        } catch (UnsupportedRuleException $e) {
            $this->assertEquals($rule, $e->getRule());

            throw $e;
        }
    }

    public function testDefaultNameSummaryDescription()
    {
        $rule = new FinalDummyRule();
        $this->assertEquals('Final Dummy', $rule->name());

        // Hit the cache
        $this->assertEquals('Final Dummy', $rule->name());

        $this->assertNull($rule->summary());
        $this->assertNull($rule->description());
    }
}

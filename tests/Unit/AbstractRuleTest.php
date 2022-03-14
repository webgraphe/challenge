<?php

namespace Webgraphe\Tests\PredicateTree\Unit;

use Webgraphe\PredicateTree\AbstractRule;
use Webgraphe\PredicateTree\Context;
use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\RuleContract;
use Webgraphe\PredicateTree\Exceptions\UnsupportedRuleException;
use Webgraphe\Tests\PredicateTree\Dummies\AbstractDummyRule;
use Webgraphe\Tests\PredicateTree\Dummies\FinalDummyRule;
use Webgraphe\Tests\PredicateTree\TestCase;

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

            public function toArray(ContextContract $context): array
            {
                return [];
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

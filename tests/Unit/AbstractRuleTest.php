<?php

namespace Webgraphe\Tests\Challenge\Unit;

use Webgraphe\Challenge\AbstractRule;
use Webgraphe\Challenge\Context;
use Webgraphe\Challenge\Contracts\ContextContract;
use Webgraphe\Challenge\Contracts\RuleContract;
use Webgraphe\Challenge\Exceptions\UnsupportedRuleException;
use Webgraphe\Tests\Challenge\Dummies\AbstractDummyRule;
use Webgraphe\Tests\Challenge\Dummies\FinalDummyRule;
use Webgraphe\Tests\Challenge\TestCase;

/**
 * @covers ::AbstractRule
 */
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

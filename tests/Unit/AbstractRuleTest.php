<?php

namespace Webgraphe\Tests\PredicateTree\Unit;

use Webgraphe\PredicateTree\AbstractRule;
use Webgraphe\PredicateTree\Context;
use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\RuleContract;
use Webgraphe\PredicateTree\Exceptions\UnsupportedRuleException;
use Webgraphe\Tests\PredicateTree\TestCase;

class AbstractRuleTest extends TestCase
{
    private function rule(): AbstractRule
    {
        return new class() extends AbstractRule {
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

    public function testUnsupportedRuleException()
    {
        $this->expectException(UnsupportedRuleException::class);

        $rule =             new class implements RuleContract {
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
        };

        try {
            AbstractRule::assertType($rule);
        } catch (UnsupportedRuleException $e) {
            $this->assertEquals($rule, $e->getRule());

            throw $e;
        }
    }
}

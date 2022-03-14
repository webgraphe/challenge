<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\RuleContract;
use Webgraphe\PredicateTree\Exceptions\UnsupportedRuleException;

abstract class AbstractRule implements RuleContract
{
    public function __construct()
    {
    }

    /**
     * @param Context $context
     * @return bool
     */
    abstract protected function evaluateProtected(Context $context): bool;

    public function toArray(ContextContract $context): array
    {
        return [];
    }

    /**
     * @param ContextContract $context
     * @return bool
     * @throws Exceptions\UnsupportedContextException
     */
    final public function evaluate(ContextContract $context): bool
    {
        return $this->evaluateProtected(Context::assertType($context));
    }

    final public function hash(ContextContract $context): string
    {
        return hash('fnv1a64', $this->marshal($context));
    }

    private function marshal(ContextContract $context): string
    {
        return $context->serialize(
            [
                'class' => static::class,
                'data' => $this->toArray($context),
            ]
        );
    }

    /**
     * @param RuleContract $rule
     * @return AbstractRule
     * @throws UnsupportedRuleException
     */
    final public static function assertType(RuleContract $rule): AbstractRule
    {
        if ($rule instanceof AbstractRule) {
            return $rule;
        }

        throw UnsupportedRuleException::create($rule);
    }
}

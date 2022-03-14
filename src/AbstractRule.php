<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\RuleContract;

abstract class AbstractRule implements RuleContract
{
    private static string $serializer;

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
     */
    final public function evaluate(ContextContract $context): bool
    {
        return $this->evaluateProtected($this->assertContext($context));
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
     * @param ContextContract $context
     * @return Context
     */
    private function assertContext(ContextContract $context): Context
    {
        return $context;
    }
}

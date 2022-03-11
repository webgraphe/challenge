<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\PredicateContract;
use Webgraphe\PredicateTree\Exceptions\ContextException;

abstract class AbstractRule implements PredicateContract
{
    public function __construct()
    {
    }

    /**
     * @param Context $context
     * @return bool
     * @throws ContextException
     */
    abstract protected function evaluateProtected(Context $context): bool;

    /**
     * @param ContextContract $context
     * @return bool
     * @throws ContextException
     */
    final public function evaluate(ContextContract $context): bool
    {
        return $this->evaluateProtected($this->assertContext($context));
    }

    public function hash(): string
    {
        return md5(get_class($this));
    }

    /**
     * @param ContextContract $context
     * @return Context
     * @throws ContextException
     */
    private function assertContext(ContextContract $context): Context
    {
        if ($context instanceof Context) {
            return $context;
        }

        $class = Context::class;

        throw ContextException::invalidContext($context, "Not a $class");
    }
}

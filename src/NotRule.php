<?php

namespace Webgraphe\PredicateTree;

class NotRule extends AbstractRule
{
    private AbstractRule $rule;

    final public function __construct(AbstractRule $rule)
    {
        parent::__construct();
        $this->rule = $rule;
    }

    /**
     * @param AbstractRule $rule
     * @return static
     */
    final public static function create(AbstractRule $rule): self
    {
        return new static($rule);
    }

    protected function evaluateProtected(Context $context): bool
    {
        return !$context->evaluate($this->rule);
    }

    public function hash(): string
    {
        return md5(get_class($this) . ':' . $this->rule->hash());
    }
}

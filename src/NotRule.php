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

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'rule' => $this->rule->toArray(),
            ]
        );
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
}

<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Exceptions\RuleException;

class NotRule extends AbstractRule
{
    private AbstractRule $rule;

    final public function __construct(AbstractRule $rule)
    {
        parent::__construct();
        $this->rule = $rule;
    }

    public function toArray(ContextContract $context): array
    {
        return array_merge(
            parent::toArray($context),
            [
                'rule' => $this->rule->hash($context),
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

    /**
     * @param Context $context
     * @return bool
     * @throws RuleException
     */
    protected function evaluateProtected(Context $context): bool
    {
        return !$context->evaluate($this->rule);
    }
}

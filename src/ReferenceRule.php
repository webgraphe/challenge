<?php

namespace Webgraphe\RuleTree;

use Webgraphe\RuleTree\Exceptions\EvaluationException;
use Webgraphe\RuleTree\Exceptions\RuleNotFoundException;

class ReferenceRule extends AbstractRule
{
    private string $reference;

    final public function __construct(string $reference)
    {
        parent::__construct();

        $this->reference = $reference;
    }

    /**
     * @param string $name
     * @return static
     */
    final public static function create(string $name): self
    {
        return new static($name);
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param Context $context
     * @return bool
     * @throws EvaluationException
     * @throws RuleNotFoundException
     */
    protected function evaluateProtected(Context $context): bool
    {
        if ($rule = $context->getRule($this->reference)) {
            return $context->evaluate($rule);
        }

        throw new RuleNotFoundException($this->reference);
    }
}

<?php

namespace Webgraphe\PredicateTree;

use Iterator;
use Webgraphe\PredicateTree\Contracts\ContextContract;

abstract class AbstractListRule extends AbstractRule implements Iterator
{
    /** @var AbstractRule[] */
    private array $rules;

    final public function __construct(AbstractRule ...$rules)
    {
        parent::__construct();
        $this->rules = $rules;
    }

    public function toArray(ContextContract $context): array
    {
        return array_merge(
            parent::toArray($context),
            [
                'rules' => array_map(fn(AbstractRule $rule) => $rule->hash($context), $this->rules)
            ]
        );
    }

    /**
     * @param AbstractRule ...$rules
     * @return static
     */
    final public static function create(AbstractRule ...$rules): self
    {
        return new static(...$rules);
    }

    public function current(): ?AbstractRule
    {
        return current($this->rules);
    }

    public function next()
    {
        next($this->rules);
    }

    public function key()
    {
        return key($this->rules);
    }

    public function valid(): bool
    {
        return null !== $this->key();
    }

    public function rewind()
    {
        reset($this->rules);
    }
}

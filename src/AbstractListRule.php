<?php

namespace Webgraphe\PredicateTree;

use Iterator;

abstract class AbstractListRule extends AbstractRule implements Iterator
{
    /** @var AbstractRule[] */
    private array $rules;

    final public function __construct(AbstractRule ...$rules)
    {
        parent::__construct();
        $this->rules = $rules;
    }

    /**
     * @param AbstractRule ...$rules
     * @return static
     */
    final public static function create(AbstractRule ...$rules): self
    {
        return new static(...$rules);
    }

    public function hash(): string
    {
        $ruleHashes = implode(':', array_map(fn(AbstractRule $rule) => $rule->hash(), $this->rules));

        return md5(parent::hash() . ':' . $ruleHashes);
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

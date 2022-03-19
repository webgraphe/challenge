<?php

namespace Webgraphe\RuleTree;

use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\Contracts\RuleContract;
use Webgraphe\RuleTree\Exceptions\UnsupportedRuleException;

abstract class AbstractRule implements RuleContract
{
    private static array $nameCache = [];

    public function __construct()
    {
    }

    /**
     * @param Context $context
     * @return bool
     */
    abstract protected function evaluateProtected(Context $context): bool;

    public function name(): string
    {
        $parts = explode('\\', get_class($this));
        $last = array_pop($parts);
        if (isset(self::$nameCache[$last])) {
            return self::$nameCache[$last];
        }

        return self::$nameCache[$last] = self::words(preg_replace('/Rule$/', '', $last));
    }

    private static function words(string $value): string
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = preg_replace('/(.)(?=[A-Z])/u', '$1 ', $value);
        }

        return trim($value);
    }

    public function summary(): ?string
    {
        return null;
    }

    public function description(): ?string
    {
        return null;
    }

    protected function data(ContextContract $context): array
    {
        return [];
    }

    final public function marshal(ContextContract $context): array
    {
        return [
            'class' => static::class,
            'summary' => $this->summary(),
            'description' => $this->description(),
            'data' => $this->data($context),
        ];
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
        return hash('fnv1a64', $context->serialize($this->marshal($context)));
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

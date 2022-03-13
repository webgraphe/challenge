<?php

namespace Webgraphe\PredicateTree;

use Webgraphe\PredicateTree\Contracts\ContextContract;
use Webgraphe\PredicateTree\Contracts\PredicateContract;
use Webgraphe\PredicateTree\Exceptions\ContextException;

abstract class AbstractRule implements PredicateContract
{
    private static string $serializer;

    public function __construct()
    {
    }

    /**
     * @param Context $context
     * @return bool
     * @throws ContextException
     */
    abstract protected function evaluateProtected(Context $context): bool;

    public function toArray(): array
    {
        return [];
    }

    /**
     * @param ContextContract $context
     * @return bool
     * @throws ContextException
     */
    final public function evaluate(ContextContract $context): bool
    {
        return $this->evaluateProtected($this->assertContext($context));
    }

    final public function hash(): string
    {
        return md5(call_user_func($this->serializer(), $this->marshal()));
    }

    private function marshal(): array
    {
        return [
            'class' => static::class,
            'serializer' => $this->serializer(),
            'data' => $this->toArray(),
        ];
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

    public static function serializer(string $serializer = null): string
    {
        if (null !== $serializer) {
            self::$serializer = $serializer;
        } else {
            self::$serializer ??= function_exists('igbinary_serialize')
                ? 'igbinary_serialize'
                : 'serialize';
        }

        return self::$serializer;
    }
}

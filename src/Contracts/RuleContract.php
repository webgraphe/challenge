<?php

namespace Webgraphe\RuleTree\Contracts;

use Webgraphe\RuleTree\Exceptions\UnsupportedContextException;

interface RuleContract
{
    /**
     * @param ContextContract $context
     * @return bool
     * @throws UnsupportedContextException
     */
    public function evaluate(ContextContract $context): bool;

    public function hash(ContextContract $context): string;

    public function name(): string;

    public function summary(): ?string;

    public function description(): ?string;

    public function toArray(ContextContract $context): array;
}

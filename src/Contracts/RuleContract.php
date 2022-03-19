<?php

namespace Webgraphe\RuleTree\Contracts;

use Exception;

interface RuleContract
{
    /**
     * @param ContextContract $context
     * @return bool
     * @throws Exception
     */
    public function evaluate(ContextContract $context): bool;

    public function hash(ContextContract $context): string;

    public function name(): string;

    public function summary(): ?string;

    public function description(): ?string;

    public function marshal(ContextContract $context): array;
}

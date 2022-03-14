<?php

namespace Webgraphe\RuleTree\Contracts;

use JsonSerializable;

interface ContextContract extends JsonSerializable
{
    public function serialize($value): string;

    public function toArray(): array;
}

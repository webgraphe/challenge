<?php

namespace Webgraphe\PredicateTree\Contracts;

use JsonSerializable;

interface ContextContract extends JsonSerializable
{
    public function serialize($value): string;

    public function toArray(): array;
}

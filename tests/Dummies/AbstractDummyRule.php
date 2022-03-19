<?php

namespace Webgraphe\Tests\Challenge\Dummies;

use Webgraphe\Challenge\AbstractRule;

abstract class AbstractDummyRule extends AbstractRule
{
    public const NAME = 'Name';
    public const SUMMARY = 'Summary';
    public const DESCRIPTION = 'Description';

    public function name(): string
    {
        return self::NAME;
    }

    public function summary(): string
    {
        return self::SUMMARY;
    }

    public function description(): string
    {
        return self::DESCRIPTION;
    }
}

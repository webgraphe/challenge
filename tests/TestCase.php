<?php

namespace Webgraphe\Tests\RuleTree;

use Exception;
use Webgraphe\RuleTree\AbstractRule;
use Webgraphe\RuleTree\Context;
use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\Tests\RuleTree\Dummies\AbstractDummyRule;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function booleanRule(bool $returnValue): AbstractDummyRule
    {
        return new class($returnValue) extends AbstractDummyRule {
            private bool $returnValue;

            public function __construct(bool $returnValue)
            {
                parent::__construct();
                $this->returnValue = $returnValue;
            }

            protected function data(ContextContract $context): array
            {
                return array_merge(
                    parent::data($context),
                    [
                        'returnValue' => $this->returnValue,
                    ]
                );
            }

            protected function evaluateProtected(Context $context): bool
            {
                return $this->returnValue;
            }
        };
    }

    protected function exceptionRule(string $message, int $code): AbstractRule
    {
        return new class($message, $code) extends AbstractDummyRule {
            private string $message;
            private int $code;

            public function __construct(string $message, int $code)
            {
                parent::__construct();
                $this->message = $message;
                $this->code = $code;
            }

            protected function data(ContextContract $context): array
            {
                return array_merge(
                    parent::data($context),
                    [
                        'message' => $this->message,
                        'code' => $this->code,
                    ]
                );
            }

            protected function evaluateProtected(Context $context): bool
            {
                throw new Exception($this->message, $this->code);
            }
        };
    }
}

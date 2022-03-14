<?php

namespace Webgraphe\RuleTree;

use Exception;
use Webgraphe\RuleTree\Contracts\ContextContract;
use Webgraphe\RuleTree\Contracts\RuleContract;
use Webgraphe\RuleTree\Exceptions\InvalidRuleNameException;
use Webgraphe\RuleTree\Exceptions\InvalidSerializerException;
use Webgraphe\RuleTree\Exceptions\RuleEvaluationException;
use Webgraphe\RuleTree\Exceptions\RuleNameConflictException;
use Webgraphe\RuleTree\Exceptions\UnsupportedContextException;

class Context implements ContextContract
{
    public const SERIALIZER_IGBINARY = 'igbinary_serialize';
    public const SERIALIZER_PHP = 'serialize';
    public const SERIALIZER_JSON_ENCODE = 'json_encode';

    public const SERIALIZERS  = [
        self::SERIALIZER_IGBINARY,
        self::SERIALIZER_PHP,
        self::SERIALIZER_JSON_ENCODE,
    ];

    public const RULE_NAME_REGEX = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/';

    /** @var array<string, Result> */
    private array $resultCache = [];
    /** @var RuleContract[] */
    private array $ruleStack = [];
    private string $serializer;
    /** @var array<string, RuleContract> $rules Associated by name */
    private $rules = [];

    private function __construct(string $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string|null $serializer
     * @return static
     * @throws InvalidSerializerException
     */
    public static function create(string $serializer = null): self
    {
        $serializer ??= function_exists(self::SERIALIZER_IGBINARY) ? self::SERIALIZER_IGBINARY : self::SERIALIZER_PHP;

        if (!function_exists($serializer)) {
            throw new InvalidSerializerException("'$serializer' does not exist");
        }

        return new static($serializer);
    }

    /**
     * @param ContextContract $context
     * @return Context
     * @throws UnsupportedContextException
     */
    public static function assertType(ContextContract $context): Context
    {
        if ($context instanceof Context) {
            return $context;
        }

        throw UnsupportedContextException::create($context);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'class' => get_class($this),
            'serializer' => $this->serializer,
            'resultCache' => array_map(fn(Result $result) => $result->toArray($this), $this->resultCache),
            'ruleStack' => array_map(fn(RuleContract $result) => $result->toArray($this), $this->ruleStack),
        ];
    }

    /**
     * @param RuleContract $rule
     * @return bool
     * @throws RuleEvaluationException
     */
    public function evaluate(RuleContract $rule): bool
    {
        $this->push($rule);
        try {
            $result = $this->resultCache[$rule->hash($this)] ??= new Result($rule, $rule->evaluate($this));
        } catch (Exception $previous) {
            throw new RuleEvaluationException("Evaluation failed", 0, $previous);
        }
        $this->pop();

        return $result->isSuccess();
    }

    /**
     * @param RuleContract $rule
     * @param string $uniqueName
     * @return static
     * @throws InvalidRuleNameException
     * @throws RuleNameConflictException
     */
    public function registerRule(RuleContract $rule, string $uniqueName): self
    {
        if (isset($this->rules[$uniqueName])) {
            throw new RuleNameConflictException($uniqueName);
        }

        if (!preg_match(self::RULE_NAME_REGEX, $uniqueName)) {
            throw new InvalidRuleNameException($uniqueName);
        }

        $this->rules[$uniqueName] = $rule;

        return $this;
    }

    public function getRule(string $name): ?RuleContract
    {
        return $this->rules[$name] ?? null;
    }

    private function push(RuleContract $rule)
    {
        $this->ruleStack[] = $rule;
    }

    private function pop()
    {
        array_pop($this->ruleStack);
    }

    /**
     * @return RuleContract[]
     */
    public function getRuleStack(): array
    {
        return $this->ruleStack;
    }

    public function serialize($value): string
    {
        return call_user_func($this->serializer, $value);
    }

    /**
     * @return string
     */
    public function getSerializer(): string
    {
        return $this->serializer;
    }
}

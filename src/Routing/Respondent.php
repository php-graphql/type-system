<?php
/**
 * This file is part of Railgun package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railgun\Routing;

use Illuminate\Support\Str;

/**
 * Class Respondent
 * @package Railgun\Routing
 */
class Respondent
{
    /**
     * @var \Closure
     */
    private $invocation;

    /**
     * @param string $definition
     * @return Respondent
     * @throws \InvalidArgumentException
     */
    public static function fromStringDefinition(string $definition): Respondent
    {
        switch (true) {
            case Str::contains($definition, '@'):
                $delimiter = '@'; break;
            case Str::contains($definition, '::'):
                $delimiter = '::'; break;
            case Str::contains($definition, '#'):
                $delimiter = '#'; break;
            default:
                $message = 'Unresolvable delimiter at "%s" string';
                throw new \InvalidArgumentException(sprintf($message, $definition));
        }

        $parts = explode($delimiter, $definition);

        if (count($parts) !== 2) {
            $message = 'Invalid callback type "%s"';
            throw new \InvalidArgumentException(sprintf($message, $definition));
        }

        return static::fromCallable([new $parts[0], $parts[1]]);
    }

    /**
     * @param callable $callable
     * @return static|Respondent
     */
    public static function fromCallable(callable $callable): Respondent
    {
        if ($callable instanceof \Closure) {
            return new static($callable);
        }

        return new static(\Closure::fromCallable($callable));
    }

    /**
     * @param string|callable $relation
     * @return Respondent
     * @throws \InvalidArgumentException
     */
    public static function new($relation): Respondent
    {
        switch (true) {
            case $relation instanceof static:
                return $relation;
            case is_callable($relation):
                return static::fromCallable($relation);
            case is_string($relation):
                return static::fromStringDefinition($relation);
        }

        throw new \InvalidArgumentException('Invalid respondent argument definition');
    }

    /**
     * Respondent constructor.
     * @param \Closure $invocation
     */
    public function __construct(\Closure $invocation)
    {
        $this->invocation = $invocation;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'action' => $this->invocation
        ];
    }

    /**
     * @return \Closure
     */
    public function toClosure(): \Closure
    {
        return $this->invocation;
    }
}

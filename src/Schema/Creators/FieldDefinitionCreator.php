<?php
/**
 * This file is part of Railgun package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Railgun\Schema\Creators;

use Serafim\Railgun\Schema\Definitions\ArgumentDefinitionInterface;
use Serafim\Railgun\Schema\Definitions\FieldDefinition;
use Serafim\Railgun\Schema\Definitions\FieldDefinitionInterface;
use Serafim\Railgun\Schema\Definitions\TypeDefinition;
use Serafim\Railgun\Support\InteractWithName;

/**
 * Class FieldDefinitionCreator
 * @package Serafim\Railgun\Schema\Creators
 */
class FieldDefinitionCreator implements CreatorInterface
{
    use InteractWithName {
        getName as private;
        hasName as private;
        getDescription as private;
        hasDescription as private;
    }
    use ProvidesTypeDefinition;
    use ProvidesNameableDefinition;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \Closure|null
     */
    private $resolver;

    /**
     * @var string|null
     */
    private $deprecationReason;

    /**
     * @var array|ArgumentDefinitionCreator[]
     */
    private $arguments = [];

    /**
     * ArgumentDefinitionCreator constructor.
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->rename($this->type = $type);
    }

    /**
     * @param string $message
     * @param null|string $version
     * @return $this
     */
    public function deprecated(string $message, ?string $version = null)
    {
        $this->deprecationReason = $message . ($version !== null ? ' since ' . $version : '');

        return $this;
    }

    /**
     * @param string $name
     * @param string $typeOf
     * @param \Closure|null $then
     * @return FieldDefinitionCreator
     */
    public function withArgument(string $name, string $typeOf, ?\Closure $then = null): FieldDefinitionCreator
    {
        $argument = (new ArgumentDefinitionCreator($typeOf))->rename($name);

        if ($then !== null) {
            $then($argument);
        }

        $this->arguments[$name] = $argument;

        return $this;
    }

    /**
     * @param \Closure $callback
     * @return FieldDefinitionCreator
     */
    public function then(\Closure $callback): FieldDefinitionCreator
    {
        $this->resolver = $callback;

        return $this;
    }

    /**
     * @return FieldDefinitionInterface
     * @internal This method is used to create a definition and should not be used during the declaration.
     */
    public function build(): FieldDefinitionInterface
    {
        $type = new TypeDefinition($this->type, $this->isNullable, $this->isList);

        $definition = new FieldDefinition($type, $this->buildArguments(), $this->resolver, $this->deprecationReason);

        $this->applyNameable(
            $definition,
            $this->getName(),
            $this->getDescription()
        );

        return $definition;
    }

    /**
     * @return \Traversable|ArgumentDefinitionInterface[]
     */
    private function buildArguments(): \Traversable
    {
        foreach ($this->arguments as $name => $argument) {
            yield $name => $argument->build();
        }
    }

    /**
     * @return string
     */
    protected function getDescriptionSuffix(): string
    {
        return 'field';
    }
}

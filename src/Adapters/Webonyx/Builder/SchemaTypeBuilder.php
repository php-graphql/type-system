<?php
/**
 * This file is part of Railt package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Railt\Adapters\Webonyx\Builder;

use GraphQL\Schema;
use Railt\Reflection\Abstraction\SchemaTypeInterface;

/**
 * Class SchemaTypeBuilder
 * @package Railt\Adapters\Webonyx\Builder
 * @property-read SchemaTypeInterface $type
 */
class SchemaTypeBuilder extends Builder
{
    /**
     * @return Schema
     */
    public function build(): Schema
    {
        return new Schema($this->createSchemaArguments());
    }

    /**
     * @return array
     */
    private function createSchemaArguments(): array
    {
        return array_merge($this->createMutation(), [
            'query' => $this->make($this->type->getQuery(), ObjectTypeBuilder::class),
        ]);
    }

    /**
     * @return array
     */
    private function createMutation(): array
    {
        if (!$this->type->hasMutation()) {
            return [];
        }

        $mutation = $this->make($this->type->getMutation(), ObjectTypeBuilder::class);

        return [
            'mutation' => $mutation
        ];
    }
}
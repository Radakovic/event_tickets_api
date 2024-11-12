<?php

namespace App\Doctrine\ApiFilter;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

/**
 * Filter both Item and Collection queries.
 */
abstract class AbstractQueryFilter implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * Apply a filter to the query builder.
     */
    abstract public function applyToQuery(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void;

    /**
     * @inheritDoc
     */
    final public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->applyToQuery($queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
    }

    /**
     * @inheritDoc
     */
    final public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        Operation $operation = null,
        array $context = []
    ): void {
        $this->applyToQuery($queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
    }
}

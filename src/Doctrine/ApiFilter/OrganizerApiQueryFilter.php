<?php

namespace App\Doctrine\ApiFilter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Organizer;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class OrganizerApiQueryFilter extends AbstractQueryFilter
{
    public function __construct(
        private readonly Security $security,
    ) {
    }
    /**
     * Filter {@see Organizer} for current user
     * @inheritDoc
     */
    public function applyToQuery(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $user = $this->security->getUser();

        if ($resourceClass !== Organizer::class || $user === null) {
            return;
        }

        assert($user instanceof User);

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $rootAlias = $queryBuilder->getRootAliases()[0];

            $queryBuilder->andWhere("$rootAlias.manager = :manager")
                ->setParameter("manager", $user);
        }
    }
}

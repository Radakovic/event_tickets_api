<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findAllManagers(): array
    {
//        $connection = $this->getEntityManager()->getConnection();
//
//        $sql = 'SELECT * FROM user WHERE JSON_CONTAINS(roles, :role) = 1';
//
//        $stmt = $connection->prepare($sql);
//        $stmt->bindValue('role', json_encode('ROLE_MANAGER'));
//        return $stmt->executeQuery()->fetchFirstColumn();

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(User::class, 'u');
        $rsm->addFieldResult('u', 'id', 'id');
        $rsm->addFieldResult('u', 'first_name', 'firstName');
        $rsm->addFieldResult('u', 'last_name', 'lastName');
        $rsm->addFieldResult('u', 'email', 'email');
        $rsm->addFieldResult('u', 'created_at', 'createdAt');

        $sql = 'SELECT * FROM user WHERE JSON_CONTAINS(roles, :role) = 1';

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('role', json_encode('ROLE_MANAGER'));

        return $query->getResult();
    }
}

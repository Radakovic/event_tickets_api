<?php

namespace App\DataFixtures;

use App\Factory\OrganizerFactory;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrganizerFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $repository = $this->userRepository;
        $managerIds = $repository->findAllManagerIds();

        $organizerManagers = $repository->findBy(['id' => $managerIds]);

        foreach ($organizerManagers as $organizerManager) {
            OrganizerFactory::createOne([
                'manager' => $organizerManager,
            ]);
        }

//        $organizerManager = $repository->find($managerIds[array_rand($managerIds)]);
//
//
//
//
//
//        OrganizerFactory::createMany(
//            5,
//            static function () use ($repository) {
//                $managerIds = $repository->findAllManagerIds();
//                $organizerManager = $repository->findBy(
//                    [
//                        'id' => $managerIds,
//                    ]
//                );
//                return ['manager' => $organizerManager];
//            }
//        );
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

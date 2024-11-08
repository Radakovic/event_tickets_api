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
        $organizers = $repository->findAllManagers();

        foreach ($organizers as $organizerManager) {
            OrganizerFactory::createOne([
                'manager' => $organizerManager,
            ]);
        }
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}

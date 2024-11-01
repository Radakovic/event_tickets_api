<?php

namespace App\DataFixtures;

use App\Factory\EventFactory;
use App\Factory\OrganizerFactory;
use App\Repository\OrganizerRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrganizerRepository $organizerRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $janeDoe = $this->userRepository->findOneBy(['email' => 'jane.doe@example.com']);
        $organizer = $this->organizerRepository->findOneBy(['manager' => $janeDoe]);

        EventFactory::createMany(
            number: 100,
            attributes: function () {
                return [
                    'organizer' => OrganizerFactory::random(),
                ];
            }
        );

        EventFactory::createMany(
            number: 10,
            attributes: function () use ($organizer) {
                return [
                    'organizer' => $organizer,
                ];
            }
        );
    }

    public function getDependencies()
    {
        return [OrganizerFixtures::class];
    }
}

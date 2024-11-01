<?php

namespace App\DataFixtures;

use App\Factory\EventFactory;
use App\Factory\OrganizerFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        EventFactory::createMany(
            number: 100,
            attributes: function () {
                return [
                    'organizer' => OrganizerFactory::random(),
                ];
            }
        );
    }

    public function getDependencies()
    {
        return [OrganizerFixtures::class];
    }
}

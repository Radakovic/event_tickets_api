<?php

namespace App\DataFixtures;

use App\Factory\OrganizerFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrganizerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        OrganizerFactory::createMany(20);
    }
}

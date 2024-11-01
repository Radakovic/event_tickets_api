<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(28);
        UserFactory::createOne([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => '$2y$13$h8C3aoTWltYiyp2q0uzuC.1db5RGYABX76wC3j4c5M4S1DK85iRT6',
            'roles' => ['ROLE_ADMIN'],
        ]);
        UserFactory::createOne([
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'email' => 'jane.doe@example.com',
            'password' => '$2y$13$h8C3aoTWltYiyp2q0uzuC.1db5RGYABX76wC3j4c5M4S1DK85iRT6',
            'roles' => ['ROLE_MANAGER'],
        ]);
    }
}

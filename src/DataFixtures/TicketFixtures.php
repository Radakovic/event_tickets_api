<?php

namespace App\DataFixtures;

use App\Factory\TicketFactory;
use App\Repository\EventRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TicketFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly EventRepository $eventRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $events = $this->eventRepository->findAll();

        foreach ($events as $event) {
            TicketFactory::createMany(
                number: 5,
                attributes: static function () use ($event) {
                    return [
                        'event' => $event,
                    ];
                }
            );
        }
    }

    public function getDependencies(): array
    {
        return [
            EventFixtures::class,
        ];
    }
}

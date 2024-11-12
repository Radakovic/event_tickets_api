<?php

namespace App\Tests\Unit\Security;

use App\Entity\Organizer;
use App\Factory\OrganizerFactory;
use App\Security\OrganizerAccessVoter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrganizerAccessVoterTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;

    private OrganizerAccessVoter $accessVoter;

    protected function setUp(): void
    {
        $securityMock = $this->createMock(Security::class);
        $this->accessVoter = new OrganizerAccessVoter($securityMock);
    }

    public function testVoterSupports(): void
    {
        $organizer = OrganizerFactory::createOne();
        self::assertTrue($this->accessVoter->support('view', $organizer));
    }
}

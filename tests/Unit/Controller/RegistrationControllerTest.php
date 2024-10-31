<?php

namespace App\Tests\Unit\Controller;

use App\Controller\RegistrationController;
use App\Service\UserRegistrationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class RegistrationControllerTest extends KernelTestCase
{
    public function testRegistrationSuccess(): void
    {
        $userData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
        ];

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('toArray')->willReturn($userData);

        $mockService = $this->createMock(UserRegistrationService::class);
        $mockService->expects($this->once())
            ->method('createUser')
            ->with($userData);
        $mockService->expects($this->once())
            ->method('welcomeEmail');

        $controller = new RegistrationController($mockService);

        $controller->register($mockRequest);
    }
}

<?php

namespace App\Controller;

use App\Service\UserRegistrationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Register new user to application
 */
class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserRegistrationServiceInterface $userRegistrationService
    ){
    }

    #[Route(
        path: '/registration',
        name: 'registration',
        methods: ['POST']
    )]
    public function register(Request $request): Response
    {
        $userData = $request->toArray();

        $this->userRegistrationService->createUser($userData);

        $this->userRegistrationService->welcomeEmail();

        return new Response(status: Response::HTTP_CREATED);
    }
}

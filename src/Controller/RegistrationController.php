<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserRegistrationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Register new user to application
 */
class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserRegistrationServiceInterface $userRegistrationService,
        private readonly ValidatorInterface $validator
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

        $roles[] = $userData['roles'] ?? 'ROLE_USER';

        $user = new User(
            firstName: $userData['firstName'],
            lastName: $userData['lastName'],
            email: $userData['email'],
            roles: $roles,
            password: $userData['password']
        );

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorsData = [];
            foreach ($errors as $key => $error) {
                $errorsData[$key] = [
                    'message' => $error->getMessage(),
                    'property' => $error->getPropertyPath(),
                ];
            }
            return new JsonResponse(
                data: $errorsData,
                status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->userRegistrationService->createUser($user);

        $this->userRegistrationService->welcomeEmail();

        return new Response(status: Response::HTTP_CREATED);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
    ){
    }

    #[Route(
        path: '/registration',
        name: 'registration',
        methods: ['POST'])
    ]
    public function register(Request $request): Response
    {
        $userData = $request->toArray();
        $roles = $userData['roles'] ?? ['ROLE_USER'];

        $user = new User(
            firstName: $userData['firstName'],
            lastName: $userData['lastName'],
            email: $userData['email'],
            roles: $roles,
        );

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $userData['password']
        );
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response(status: Response::HTTP_CREATED);
    }
}

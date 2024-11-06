<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class UserManagersProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
//        $user = $this->security->getUser();
//
//        if (!$user instanceof UserInterface && !$this->security->isGranted('ROLE_ADMIN')) {
//            return null;
//        }

        return $this->userRepository->findAllManagers();
    }
}

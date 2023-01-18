<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiService
{
    private JWTTokenManagerInterface $jwtManager;
    private TokenStorageInterface $tokenStorageInterface;
    private $decodedJwtToken;
    private User $user;

    public function __construct(
        TokenStorageInterface $tokenStorageInterface,
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepository
    ) {
        $this->jwtManager = $jwtManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
        $this->decodedJwtToken = $this->jwtManager->decode($this->tokenStorageInterface->getToken());

        // The current user
        $this->user = $userRepository->findOneBy([
            'email' => $this->decodedJwtToken['email']
        ]);
    }

    public function apiUser(): User
    {
        return $this->user;
    }
}
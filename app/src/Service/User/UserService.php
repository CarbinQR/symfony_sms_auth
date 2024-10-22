<?php

namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(readonly EntityManagerInterface $entityManager)
    {

    }

    public function findOrCreateUserByPhone(string $phoneNumber): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $phoneNumber]);
        if (!$user) {
            $user = new User();
            $user->setPhone($phoneNumber);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }

    public function makeItem(User $user): array
    {
        return [
            'id' => $user->getUserIdentifier(),
            'phone' => $user->getPhone(),
            'nickname' => $user->getNickname(),
        ];
    }
}
<?php

namespace App\Service\User;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserService
{
    protected ServiceEntityRepositoryInterface $repository;

    protected const DEFAULT_PROVIDER = 'turbo_sms';

    public function __construct(readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(User::class);
    }

    public function findOrCreateUserByPhone(string $phoneNumber): User
    {
        try {
            $user = $this->repository->findOneBy(['phone' => $phoneNumber]);
            if (!$user) {
                $user = new User();
                $user->setPhone($phoneNumber);
                $user->setProvider(self::DEFAULT_PROVIDER);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_CONFLICT, $e->getMessage());
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
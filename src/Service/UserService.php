<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\InvalidArgumentException;
use App\Exception\NotFoundException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * UserService constructor.
     */
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->validator = $validator;
    }

    public function save(User $user): void
    {
        $errors = $this->validator->validate($user);
        if ($errors->count()) {
            throw new InvalidArgumentException((string) $errors);
        }
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $user->getPassword()
        ));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function create(string $email, string $password): User
    {
        $user = new User($email, $password);
        $this->save($user);

        return $user;
    }

    public function update(string $id, User $preUser): User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new NotFoundException();
        }
        $user->setPassword($preUser->getPassword());
        $user->setUsername($preUser->getUsername());

        $errors = $this->validator->validate($user);
        if ($errors->count()) {
            throw new InvalidArgumentException((string) $errors);
        }

        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $user->getPassword()
        ));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function get(string $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function getAll(): array
    {
        return $this->userRepository->findAll();
    }

    public function delete(string $id): void
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new NotFoundException();
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}

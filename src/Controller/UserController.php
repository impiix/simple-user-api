<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\InvalidArgumentException;
use App\Exception\NotFoundException;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class DefaultController
 * @package App\Controller
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", methods={"GET"})
     */
    public function viewAll(UserService $userService): JsonResponse
    {
        $users = $userService->getAll();

        return $this->json($users, 200, [], ['groups' => 'read']);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function view(UserService $userService, $id): JsonResponse
    {
        $user = $userService->get($id);
        if (!$user) {
            return $this->json(['error' => 'Not found'], 404);
        }
        return $this->json($user,200, [], ['groups' => 'read']);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(UserService $userService, $id): JsonResponse
    {
        try {
            $userService->delete($id);
        } catch (NotFoundException $e) {
            return $this->json(['error' => 'Not found'], 404);
        }
        return $this->json(['status' => 'ok'], 202);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     */
    public function update(
        SerializerInterface $serializer,
        UserService $userService, $id,
        Request $request
    ): JsonResponse {
        $content = $request->getContent();
        $user = $serializer->deserialize($content, User::class, 'json', ['groups' => 'update'] );
        /**
         * @var User $user
         */
        try {
            $user = $userService->update($id, $user);
        } catch (NotFoundException $e) {
            return $this->json(['error' => 'Not found'], 404);
        }
        return $this->json($user,202, [], ['groups' => 'read']);
    }

    /**
     * @Route("", methods={"POST"})
     */
    public function create(SerializerInterface $serializer, UserService $userService, Request $request): JsonResponse
    {
        $content = $request->getContent();
        $user = $serializer->deserialize($content, User::class, 'json', ['groups' => 'write'] );
        /**
         * @var User $user
         */
        try {
            $userService->save($user);
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        return $this->json($user,201, [], ['groups' => 'read']);
    }
}

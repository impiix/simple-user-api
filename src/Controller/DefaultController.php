<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\InvalidArgumentException;
use App\Service\UserService;
use PhpParser\JsonDecoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class DefaultController extends AbstractController
{
    /**
     * @Route("/info", name="user_info")
     */
    public function info(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'not logged in']);
        }

        return $this->json($user, 200, [], ['groups' => 'read']);
    }

    /**
     * @Route("/register", methods={"POST"})
     */
    public function register(SerializerInterface $serializer, UserService $userService, Request $request): JsonResponse
    {
        $content = $request->getContent();
        $user = $serializer->deserialize($content, User::class, 'json', ['groups' => 'write']);
        /**
         * @var User $user
         */
        try {
            $userService->save($user);
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        return $this->json($user,202, [], ['groups' => 'read']);
    }

    /**
     * @Route("/login", methods={"POST"}, name="app_login")
     */
    public function login(): JsonResponse
    {
        return $this->json(['status' => 'ok']);
    }
}

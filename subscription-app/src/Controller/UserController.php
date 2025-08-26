<?php

namespace App\Controller;

use App\Service\UserService;
use App\Entity\User;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function __construct(
        private UserService $userService,
        private SerializerInterface $serializer
    ) {}

    #[Route('/api/adduser', name: 'api_user_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return new JsonResponse(
                ['error' => 'Email and password are required'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $user = $this->userService->createUser(
                $data['email'],
                $data['password'],
                $data['firstName'] ?? null,
                $data['lastName'] ?? null,
                $data['roles'] ?? ['ROLE_USER']
            );

            $json = $this->serializer->serialize(
                $user,
                'json',
                SerializationContext::create()->setGroups(['details'])
            );

            return new JsonResponse($json, Response::HTTP_CREATED, [], true);

        } catch (\Throwable $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}

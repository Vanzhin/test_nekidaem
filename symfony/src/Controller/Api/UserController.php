<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'app_api_users')]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $users = [];

        foreach ($userRepository->findAllGenerator() as $user) {
            $users[] = $user;

        }
        return $this->json($users, 200, [], ['groups' => 'main']);
    }

    #[Route('/api/user/{id<\d+>}', name: 'app_api_user_show')]
    public function show(UserRepository $userRepository, Request $request): JsonResponse
    {
        $user = $userRepository->find($request->get('id'));
        if ($user) {
            return $this->json($user, 200, [], ['groups' => 'main']);
        }
        return $this->json('error: no user with id '. $request->get('id'), 401
        );
    }
}
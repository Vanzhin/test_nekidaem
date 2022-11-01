<?php

namespace App\Controller\Api;

use App\Entity\Blog;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        return $this->json('error: no user with id ' . $request->get('id'), 401);
    }

    #[Route('/api/user/{user<\d+>}/read/{post<\d+>}', name: 'app_api_user_read')]
    public function toggleReadPost(EntityManagerInterface $em, User $user, Post $post): JsonResponse
    {

        try {
            if($user->getReadPosts()->contains($post)){
                $user->removeReadPost($post);
                $message = "unread";
            }else{
                $user->addReadPost($post);
                $message = "read";

            };
            $em->persist($user);
            $em->flush();
            return $this->json([$message, $post, $user], 200, [], ['groups' => 'main']);

        } catch (\Exception $e){
            return $this->json($e->getMessage(), 401);

        }
    }
    #[Route('/api/user/{user<\d+>}/subscribe/{blog<\d+>}', name: 'app_api_user_subscribe')]
    public function toggleBlogSubscribed(EntityManagerInterface $em, User $user, Blog $blog): JsonResponse
    {

        try {
            if($user->getBlogs()->contains($blog)){
                $user->removeBlog($blog);
                $message = "unsubscribed";
            }else{
                $user->addBlog($blog);
                $message = "subscribed";

            };
            $em->persist($user);
            $em->flush();
            return $this->json([$message, $blog, $user], 200, [], ['groups' => 'main']);

        } catch (\Exception $e){
            return $this->json($e->getMessage(), 401);

        }
    }
}

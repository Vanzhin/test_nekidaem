<?php

namespace App\Controller\Api;

use App\Entity\Blog;
use App\Entity\Post;
use App\Repository\BlogRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostController extends AbstractController
{
    #[Route('/api/post', name: 'app_api_post')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Api/PostController.php',
        ]);
    }

    #[Route('/api/post/create', name: 'app_api_post_create', methods: 'POST')]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {

        $params = $request->request->all();
        try {
            $blog = $em->find(Blog::class, $params['blog_id']);
            $post = new Post();
            $post->setBlog($blog)
                ->setTitle($params['title'])
                ->setContent($params['content']);

            $errors = $validator->validate($post);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                return $this->json(['status' => 'error', 'message' => $errorsString]);
            }
            $em->persist($post);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json($e->getMessage());
        }
        return $this->json($post, 200, [], ['groups' => ['main']]);
    }

    #[Route('/api/post/delete/{id<\d+>}', name: 'app_api_post_delete', methods: 'DELETE')]
    public function delete(int $id, PostRepository $postRepository): JsonResponse
    {
        $post = $postRepository->find($id);

        try {
            $postRepository->remove($post, true);
        } catch (\Exception $e) {
            return $this->json($e->getMessage());
        }
        return $this->json($post, 200, [], ['groups' => ['main']]);
    }

    #[Route('/api/post/update/{id<\d+>}', name: 'app_api_post_update', methods: 'POST')]
    public function update(Request $request, int $id, PostRepository $postRepository, ValidatorInterface $validator): JsonResponse
    {

        try {
            $post = $postRepository->find($id);
            if ($request->request->has('content')) {
                $post->setContent($request->request->get('content'));
            }
            if ($request->request->has('title'))
                $post->setTitle($request->request->get('title'));
            $postRepository->save($post, true);
            $errors = $validator->validate($post);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                return $this->json(['status' => 'error', 'message' => $errorsString]);
            }
        } catch (\Exception $e) {
            return $this->json($e->getMessage());
        }
        return $this->json($post, 200, [], ['groups' => ['main']]);
    }
}

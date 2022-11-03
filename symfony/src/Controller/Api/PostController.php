<?php

namespace App\Controller\Api;

use App\Entity\Blog;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Validator\EntityValidator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/api/post/create', name: 'app_api_post_create', methods: 'POST')]
    public function create(Request $request, EntityManagerInterface $em, EntityValidator $validator): JsonResponse
    {

        $params = $request->request->all();
        try {
            $blog = $em->find(Blog::class, $params['blog_id']);
            $post = new Post();
            $post->setBlog($blog)
                ->setTitle($params['title'])
                ->setContent($params['content']);

            if ($validator->validate($post)) {
                return $this->json(['status' => 'error', 'message' => $validator->validate($post)]);
            }
            $em->persist($post);
            $em->flush();
        } catch (\Exception $e) {
            return $this->json($e->getMessage());
        }
        return $this->json($post, 200, [], ['groups' => ['main']]);
    }

    #[Route('/api/post/delete/{post<\d+>}', name: 'app_api_post_delete', methods: 'DELETE')]
    public function delete(Post $post, PostRepository $postRepository): JsonResponse
    {
        try {
            $postRepository->remove($post, true);
        } catch (\Exception $e) {
            return $this->json($e->getMessage());
        }
        return $this->json($post, 200, [], ['groups' => ['main']]);
    }

    #[Route('/api/post/update/{post<\d+>}', name: 'app_api_post_update', methods: 'POST')]
    public function update(Request $request, Post $post, PostRepository $postRepository, EntityValidator $validator): JsonResponse
    {
        try {
            if ($request->request->has('content')) {
                $post->setContent($request->request->get('content'));
            }
            if ($request->request->has('title')){
                $post->setTitle($request->request->get('title'));

            }
            if ($validator->validate($post)) {
                return $this->json(['status' => 'error', 'message' => $validator->validate($post)]);
            }
            $postRepository->save($post, true);

        } catch (\Exception $e) {
            return $this->json($e->getMessage());
        }
        return $this->json($post, 200, [], ['groups' => ['main']]);
    }

    #[Route('/api/posts/subscribed/{user<\d+>}', name: 'app_api_post_subscribed')]
    public function postsSubscribedByUser(PostRepository $postRepository, PaginatorInterface $paginator, Request $request, User $user): Response
    {
        $posts = $postRepository->findPostsQuery($user);
        $pagination = $paginator->paginate(
            $posts, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        $json = $this->json(['posts'=>$pagination, 'page'=>$request->query->getInt('page', 1)], 200, [], ['groups' => 'main']);
        return ($json);
    }

}

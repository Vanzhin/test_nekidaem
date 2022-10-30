<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_admin_user')]
    public function index(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $users = [];

        foreach ($userRepository->findAllGenerator() as $user) {
            $users[] = $user->jsonSerialize();

        }
        return $this->json($users);
    }

    #[Route('/admin/user/create', name: 'app_admin_user_create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(UserFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            /**
             * @var User $user
             */
            $user = $form->getData();
            $em->persist($user);
            $em->flush();
            $this->addFlash('user_message', 'user created');
        }

            return $this->render('admin/user/create.html.twig', [
                'userForm' => $form->createView()

            ]);
    }
}

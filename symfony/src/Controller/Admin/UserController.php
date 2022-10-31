<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

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
            $blog = new Blog();
            $blog->setTitle($user->getName().'\'s blog');
            $user->setBlog($blog);
            $em->persist($user);
            $em->persist($blog);
            $em->flush();
            $this->addFlash('user_message', 'user created');
        }

            return $this->render('admin/user/create.html.twig', [
                'userForm' => $form->createView()

            ]);
    }
}

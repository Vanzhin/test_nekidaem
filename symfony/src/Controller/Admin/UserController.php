<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    #[Route('/admin/user/create', name: 'app_admin_user_create')]
    public function create(Request $request, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
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
            $dispatcher->dispatch(new UserRegisteredEvent($user));
            $this->addFlash('user_message', 'user created');
        }

            return $this->render('admin/user/create.html.twig', [
                'userForm' => $form->createView()

            ]);
    }
}

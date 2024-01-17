<?php

// src/Controller/UserController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="user_index")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/users/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/users/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/users/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/admin/users/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

//    /**
//     * @Route("/admin/users/{id}", name="user_show", methods={"GET"})
//     */
//    public function show(User $user): Response
//    {
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
//
//        return $this->render('user/show.html.twig', [
//            'user' => $user,
//            'roles' => $user->getRoles(),
//            'is_admin' => $this->isGranted('ROLE_ADMIN'),
//            'is_user' => $this->isGranted('ROLE_USER'),
//            'is_super_admin' => $this->isGranted('ROLE_SUPER_ADMIN'),
//            'is_guest' => $this->isGranted('ROLE_GUEST'),
//            'is_anonymous' => $this->isGranted('ROLE_ANONYMOUS'),
//            'is_authenticated' => $this->isGranted('IS_AUTHENTICATED'),
//            'is_authenticated_remember_me' => $this->isGranted('IS_AUTHENTICATED_REMEMBER_ME'),
//            'is_authenticated_anonymous' => $this->isGranted('IS_AUTHENTICATED_ANONYMOUS'),
//            'is_authenticated_simple' => $this->isGranted('IS_AUTHENTICATED_SIMPLE'),
//            'is_granted' => $this->isGranted('ROLE_ADMIN'),
//            'is_granted2' => $this->isGranted('ROLE_USER'),
//        ]);
//
//        //return $this->render('user/show.html.twig', ['user' => $user]);
//
//        //return new Response($user->getRoles());
//    }
}

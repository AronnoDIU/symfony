<?php

// src/Controller/UserController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_index")
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
     * @Route("/users/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

//        $user = new User();
        $adminUser = new User();
        $adminUser->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $regularUser = new User();
        $regularUser->setRoles(['ROLE_USER']);

        $form = $this->createForm(UserType::class, $adminUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($adminUser);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $adminUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit", methods={"GET","POST"})
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
     * @Route("/users/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/users/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Check if the user has the ROLE_ADMIN role
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'roles' => $user->getRoles(),]);
        }

        // Check if the user has the ROLE_USER role
        if ($this->isGranted('ROLE_USER')) {
            return $this->render('user/show.html.twig', [
                'user' => $user,
                'roles' => $user->getRoles(),]);
        }

        // If the user does not have any of the roles, return an error message
        return $this->render('error.html.twig', [
            'message' => 'You do not have permission to access this page.',
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

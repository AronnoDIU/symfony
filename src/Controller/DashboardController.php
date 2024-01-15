<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_dashboard")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Admin logic here
        $username = $this->getUser()->getUsername();

        return $this->render('dashboard/admin.html.twig', ['username' => $username]);
    }

    /**
     * @Route("/user", name="user_dashboard")
     */
    public function user(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // User logic here
        $username = $this->getUser()->getUsername();

        return $this->render('dashboard/user.html.twig', ['username' => $username]);
    }

    /**
     * @Route("/guest", name="guest_dashboard")
     */
    public function guest(): Response
    {
        // Guest logic here
        return $this->render('dashboard/guest.html.twig');
    }
}

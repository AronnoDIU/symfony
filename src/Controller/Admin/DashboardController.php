<?php

// src/Controller/DashboardController.php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="app_admin")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('READ', null, 'Access Denied');
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}

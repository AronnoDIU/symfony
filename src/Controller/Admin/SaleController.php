<?php

// src/Controller/Admin/SaleController.php

namespace App\Controller\Admin;

use App\Entity\Sale;
use App\Form\SaleType;
use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/sale")
 */
class SaleController extends AbstractController
{
    /**
     * @Route("/", name="app_sale_index", methods={"GET"})
     */
    public function index(SaleRepository $saleRepository): Response
    {
        return $this->render('sale/index.html.twig', [
            'sales' => $saleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_sale_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SaleRepository $saleRepository): Response
    {
        $sale = new Sale();
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saleRepository->add($sale, true);

            return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sale/new.html.twig', [
            'sale' => $sale,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_sale_show", methods={"GET"})
     */
    public function show(Sale $sale): Response
    {
        return $this->render('sale/show.html.twig', [
            'sale' => $sale,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_sale_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sale $sale, SaleRepository $saleRepository): Response
    {
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saleRepository->add($sale, true);

            return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sale/edit.html.twig', [
            'sale' => $sale,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_sale_delete", methods={"POST"})
     */
    public function delete(Request $request, Sale $sale, SaleRepository $saleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sale->getId(), $request->request->get('_token'))) {
            $saleRepository->remove($sale, true);
        }

        return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
    }
}

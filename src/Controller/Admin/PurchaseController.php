<?php

// src/Controller/Admin/PurchaseController.php

namespace App\Controller\Admin;

use App\Entity\Purchase;
use App\Form\PurchaseType;
use App\Repository\PurchaseRepository;
use App\Service\StockUpdaterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/purchase")
 */
class PurchaseController extends AbstractController
{
    private StockUpdaterService $stockUpdaterService;

    public function __construct(StockUpdaterService $stockUpdaterService)
    {
        $this->stockUpdaterService = $stockUpdaterService;
    }

    /**
     * @Route("/", name="app_purchase_index", methods={"GET"})
     */
    public function index(PurchaseRepository $purchaseRepository): Response
    {
        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchaseRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_purchase_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $purchase = new Purchase();
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set status to 'Draft' explicitly (if not already set in the form)
            $purchase->setStatus('Draft');

            // Update stock after purchase creation
            $this->stockUpdaterService->updateStockFromPurchase($purchase);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($purchase);
            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('purchase/new.html.twig', [
            'purchase' => $purchase,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_purchase_show", methods={"GET"})
     */
    public function show(Purchase $purchase): Response
    {
        return $this->render('purchase/show.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * @Route("/{id}/approve", name="app_purchase_approve", methods={"POST"})
     */
    public function approve(Request $request, Purchase $purchase, PurchaseRepository $purchaseRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('approve' . $purchase->getId(), $request->request->get('_token'))) {
            $purchase->setStatus('approved');

            // Update stock after approval
            $this->stockUpdaterService->updateStockFromPurchase($purchase);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/edit", name="app_purchase_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Purchase $purchase): Response
    {
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update stock after purchase edit
            $this->stockUpdaterService->updateStockFromPurchase($purchase);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('purchase/edit.html.twig', [
            'purchase' => $purchase,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_purchase_delete", methods={"POST"})
     */
    public function delete(Request $request, Purchase $purchase): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchase->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchase);

            // Update stock after purchase deletion
            $this->stockUpdaterService->updateStockFromPurchase($purchase);

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_purchase_index', [], Response::HTTP_SEE_OTHER);
    }
}

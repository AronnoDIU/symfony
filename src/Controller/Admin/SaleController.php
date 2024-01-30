<?php

// src/Controller/Admin/SaleController.php

namespace App\Controller\Admin;

use App\Entity\Sale;
use App\Event\SaleEvent;
use App\Form\SaleType;
use App\Repository\SaleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/sale")
 */
class SaleController extends AbstractController
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/", name="app_sale_index", methods={"GET"})
     */
    public function index(SaleRepository $saleRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('sale/index.html.twig', [
            'sales' => $saleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_sale_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $sale = new Sale();
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set status to 'Draft' explicitly (if not already set in the form)
            $sale->setStatus('Draft');

            // Update stock after sales creation
//            $this->stockUpdaterService->updateStockFromSale($sale);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sale);
            $entityManager->flush();

            // Dispatch the SaleEvent after the sale is created
            $saleEvent = new SaleEvent($sale);
            $this->eventDispatcher->dispatch($saleEvent, SaleEvent::NAME);

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
     * @Route("/{id}/approve", name="app_sale_approve", methods={"POST"})
     */
    public function approve(Request $request, Sale $sale, SaleRepository $saleRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('approve' . $sale->getId(), $request->request->get('_token'))) {
            $sale->setStatus('approved');

            // Update stock after approval
//            $this->stockUpdaterService->updateStockFromSale($sale);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Dispatch the SaleEvent after the sale is approved
            $this->eventDispatcher->dispatch(new SaleEvent($sale), SaleEvent::NAME);
        }

        return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/edit", name="app_sale_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sale $sale, SaleRepository $saleRepository): Response
    {
        $form = $this->createForm(SaleType::class, $sale);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $saleRepository->add($sale, true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Dispatch the SaleEvent after the sale is edited
            $saleEvent = new SaleEvent($sale);
            $this->eventDispatcher->dispatch($saleEvent, SaleEvent::NAME);

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
    public function delete(Request $request, Sale $sale): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sale->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sale);

            $entityManager->flush();

            // Dispatch the SaleEvent after the sale is deleted
            $saleEvent = new SaleEvent($sale);
            $this->eventDispatcher->dispatch($saleEvent, SaleEvent::NAME);

            // Update stock after sale deletion
//            $this->stockUpdaterService->updateStockFromSale($sale);

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sale_index', [], Response::HTTP_SEE_OTHER);
    }
}

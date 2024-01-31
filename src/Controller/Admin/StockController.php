<?php

// src/Controller/Admin/StockController.php

namespace App\Controller\Admin;

use App\Entity\Sale;
use App\Entity\Stock;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/stock")
 */
class StockController extends AbstractController
{
    /**
     * @Route("/", name="app_stock_index", methods={"GET"})
     */
    public function index(StockRepository $stockRepository): Response
    {
        return $this->render('stock/index.html.twig', [
            'stocks' => $stockRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_stock_show", methods={"GET"})
     */
    public function show(Stock $stock): Response
    {
//        $approvedSales = $stock->getSales()->filter(function (Sale $sale) {
//            return $sale->getStatus() === 'Approve';
//        });

        return $this->render('stock/show.html.twig', [
            'stock' => $stock,
//            'approvedSales' => $approvedSales,
        ]);
    }
}

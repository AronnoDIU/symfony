<?php

// src/Controller/Admin/ProductController.php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\RedirectResponse;
//use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("admin/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="app_product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, Request $request, SessionInterface $session): Response
    {
        // Get the search query from the session
        $searchQuery = $session->get('product_search_query', '');

        // Get the search query from the request
        $newSearchQuery = $request->query->get('search', '');

        // If a new search query is present, update the session
        if ($newSearchQuery !== '') {
            $session->set('product_search_query', $newSearchQuery);
            // Redirect to prevent resubmission on page refresh
            return new RedirectResponse($this->generateUrl('app_product_index'));
        }

        // If a search query is present, and it's numeric, attempt to find the product
        if ($searchQuery !== '' && is_numeric($searchQuery)) {
            $product = $productRepository->find($searchQuery);

            // If the product is not found, throw a NotFoundHttpException
            if (!$product instanceof Product) {
                throw $this->createNotFoundException('Product not found');
            }

            // Clear the search query session variable
            $session->remove('product_search_query');

            // Redirect to the show page of the found product
            return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        }

        // Otherwise, retrieve all products
        $products = $productRepository->findAll();

        // Render index template with products
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/new", name="app_product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    // Clear search query from session and redirect to index
//    public function clearSearchQuery(SessionInterface $session): Response
//    {
//        $session->remove('product_search_query');
//        return $this->redirectToRoute('app_product_index');
//    }
}

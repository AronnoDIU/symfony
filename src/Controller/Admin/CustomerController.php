<?php

// src/Controller/Admin/CustomerController.php

namespace App\Controller\Admin;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/customer")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="app_customer_index", methods={"GET"})
     * @throws InvalidArgumentException
     */
    public function index(CustomerRepository $customerRepository, CacheInterface $cache): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $cache->get('customer_index', function () use ($customerRepository) {
            return $this->render('customer/index.html.twig', [
                'customers' => $customerRepository->findAll(),
            ]);
        });
    }

    /**
     * @Route("/new", name="app_customer_new", methods={"GET", "POST"})
     * @throws InvalidArgumentException
     */
    public function new(Request $request, CustomerRepository $customerRepository, CacheInterface $cache): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer, true);
            // Invalidate cache associated with customer index page
            $cache->delete('customer_index');
            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/{id}", name="app_customer_show", methods={"GET"})
     */
    public function show(Customer $customer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('customer/show.html.twig', [
            'customer' => $customer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_customer_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerRepository->add($customer, true);

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_customer_delete", methods={"POST"})
     */
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->request->get('_token'))) {
            $customerRepository->remove($customer, true);
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}

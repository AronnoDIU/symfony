<?php

namespace App\Controller\Admin;

use App\Entity\Unit;
use App\Form\UnitType;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/unit")
 */
class UnitController extends AbstractController
{
    /**
     * @Route("/", name="app_unit_index", methods={"GET"})
     */
    public function index(UnitRepository $unitRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('unit/index.html.twig', [
            'units' => $unitRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_unit_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UnitRepository $unitRepository): Response
    {
        $unit = new Unit();
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unitRepository->add($unit, true);

            return $this->redirectToRoute('app_unit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('unit/new.html.twig', [
            'unit' => $unit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_unit_show", methods={"GET"})
     */
    public function show(Unit $unit): Response
    {
        return $this->render('unit/show.html.twig', [
            'unit' => $unit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_unit_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Unit $unit, UnitRepository $unitRepository): Response
    {
        $form = $this->createForm(UnitType::class, $unit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $unitRepository->add($unit, true);

            return $this->redirectToRoute('app_unit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('unit/edit.html.twig', [
            'unit' => $unit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_unit_delete", methods={"POST"})
     */
    public function delete(Request $request, Unit $unit, UnitRepository $unitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$unit->getId(), $request->request->get('_token'))) {
            $unitRepository->remove($unit, true);
        }

        return $this->redirectToRoute('app_unit_index', [], Response::HTTP_SEE_OTHER);
    }
}

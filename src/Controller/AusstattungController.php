<?php

namespace App\Controller;

use App\Entity\Ausstattung;
use App\Form\AusstattungType;
use App\Repository\AusstattungRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/ausstattung")
 * @isGranted({"ROLE_ADMIN"})
 */
class AusstattungController extends AbstractController
{
    /**
     * @Route("/", name="ausstattung_index", methods={"GET"})
     */
    public function index(AusstattungRepository $ausstattungRepository): Response
    {
        return $this->render('ausstattung/index.html.twig', [
            'ausstattungs' => $ausstattungRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="ausstattung_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ausstattung = new Ausstattung();
        $form = $this->createForm(AusstattungType::class, $ausstattung);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ausstattung);
            $entityManager->flush();

            return $this->redirectToRoute('ausstattung_index');
        }

        return $this->render('ausstattung/new.html.twig', [
            'ausstattung' => $ausstattung,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ausstattung_show", methods={"GET"})
     */
    public function show(Ausstattung $ausstattung): Response
    {
        return $this->render('ausstattung/show.html.twig', [
            'ausstattung' => $ausstattung,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ausstattung_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ausstattung $ausstattung): Response
    {
        $form = $this->createForm(AusstattungType::class, $ausstattung);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ausstattung_index');
        }

        return $this->render('ausstattung/edit.html.twig', [
            'ausstattung' => $ausstattung,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ausstattung_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ausstattung $ausstattung): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ausstattung->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ausstattung);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ausstattung_index');
    }
}

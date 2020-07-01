<?php

namespace App\Controller;

use App\Entity\Addresse;
use App\Form\AddresseType;
use App\Repository\AddresseRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/addresse")
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_FOOD')")
 */
class AddresseController extends AbstractController
{
    /**
     * @Route("/autoComplete", name="addresse_auto_complete", methods={"GET"})
     */
    public function forAutoComplete(Request $request, AddresseRepository $addresseRepo)
    {
        $addresses = $addresseRepo->findAllMatching($request->query->get('query'));
        return $this->json(['addresses' => $addresses], 200, [], ['groups' => ['address:autocomplete']]);
    }

    /**
     * @Route("/", name="addresse_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, AddresseRepository $adrRepo): Response
    {
        $query = $adrRepo->getQueryFromRequest($request);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('addresse/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/new", name="addresse_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $addresse = new Addresse();
        $form = $this->createForm(AddresseType::class, $addresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($addresse);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new Response('OK;'.$addresse->getForAutoComplete());
            }

            $this->addFlash('success', "Die Adresse wurde angelegt.");
            return $this->redirectToRoute('addresse_index');
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('addresse/_form.html.twig', [
                'addresse' => $addresse,
                'klo_ki_form_action' => $this->generateUrl('addresse_new'),
                'form' => $form->createView()
            ]);
        }


        return $this->render('addresse/new.html.twig', [
            'addresse' => $addresse,
            'klo_ki_form_action' => $this->generateUrl('addresse_new'),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="addresse_show", methods={"GET"})
     */
    public function show(Addresse $addresse): Response
    {
        return $this->render('addresse/show.html.twig', [
            'adr' => $addresse,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="addresse_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Addresse $addresse): Response
    {
        $form = $this->createForm(AddresseType::class, $addresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Die Adresse wurde gespeichert.");
            return $this->redirectToRoute('addresse_index');
        }

        return $this->render('addresse/edit.html.twig', [
            'adr' => $addresse,
            'klo_ki_form_action' => $this->generateUrl('addresse_edit', ['id' => $addresse->getId()]),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="addresse_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Addresse $addresse): Response
    {
        if ($this->isCsrfTokenValid('delete'.$addresse->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($addresse);
            $entityManager->flush();
            $this->addFlash('success', "Die Adresse wurde gelöscht.");

        }

        return $this->redirectToRoute('addresse_index');
    }
}

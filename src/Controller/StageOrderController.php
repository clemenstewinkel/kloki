<?php

namespace App\Controller;

use App\Entity\StageOrder;
use App\Form\BestuhlungsplanType;
use App\Form\StageOrderType;
use App\Repository\StageOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

/**
 * @Route("/stage/order")
 */
class StageOrderController extends AbstractController
{

    private function handleUploadedPdf(&$form,StageOrder &$stageOrder)
    {
        /** @var UploadedFile $pdfFile */
        $pdfFile = $form['pdfFilePath']->getData();

        $pdfFileName = basename(tempnam($this->getParameter('pdf_file_directory'), 'stageOrder_'));
        $pngFileName = basename(tempnam($this->getParameter('png_file_directory'), 'stageOrder_'));

        // Move the file to the directory where brochures are stored
        try {
            $pdfFile->move(
                $this->getParameter('pdf_file_directory'),
                $pdfFileName
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        $im = new \Imagick($this->getParameter('pdf_file_directory') . $pdfFileName . '[0]');
        $im->setImageFormat('png');
        $im->resizeImage(100,150, \Imagick::FILTER_CUBIC, 1.0);
        file_put_contents($this->getParameter('png_file_directory') . $pngFileName, $im );
        $stageOrder->setPdfFileName($pdfFileName);
        $stageOrder->setPngFileName($pngFileName);
    }



    /**
     * @Route("/", name="stage_order_index", methods={"GET"})
     */
    public function index(StageOrderRepository $stageOrderRepository): Response
    {
        return $this->render('stage_order/index.html.twig', [
            'stage_orders' => $stageOrderRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="stage_order_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $stageOrder = new StageOrder();
        $form = $this->createForm(StageOrderType::class, $stageOrder)
            ->add('pdfFilePath', FileType::class, [
                'label' => 'Bestuhlungsplan (PDF file)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => ['application/pdf','application/x-pdf'],
                        'mimeTypesMessage' => 'Das ist kein gültiges PDF-Dokument (max. 4096 kB)!',
                    ])
                ],
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleUploadedPdf($form, $stageOrder);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($stageOrder);
            $entityManager->flush();

            return $this->redirectToRoute('stage_order_index');
        }

        return $this->render('stage_order/new.html.twig', [
            'stage_order' => $stageOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="stage_order_show", methods={"GET"})
     */
    public function show(StageOrder $stageOrder): Response
    {
        return $this->render('stage_order/show.html.twig', [
            'stage_order' => $stageOrder,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="stage_order_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, StageOrder $stageOrder): Response
    {
        $form = $this->createForm(StageOrderType::class, $stageOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('stage_order_index');
        }

        return $this->render('stage_order/edit.html.twig', [
            'stage_order' => $stageOrder,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="stage_order_delete", methods={"DELETE"})
     */
    public function delete(Request $request, StageOrder $stageOrder): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stageOrder->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($stageOrder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('stage_order_index');
    }
}

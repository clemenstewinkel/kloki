<?php

namespace App\Controller;

use App\Entity\Bestuhlungsplan;
use App\Form\BestuhlungsplanType;
use App\Repository\BestuhlungsplanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/bestuhlungsplan")
 * @isGranted({"ROLE_ADMIN"})
 */
class BestuhlungsplanController extends AbstractController
{
    /**
     * @Route("/", name="bestuhlungsplan_index", methods={"GET"})
     */
    public function index(BestuhlungsplanRepository $bestuhlungsplanRepository): Response
    {
        return $this->render('bestuhlungsplan/index.html.twig', [
            'bestuhlungsplans' => $bestuhlungsplanRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="bestuhlungsplan_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $bestuhlungsplan = new Bestuhlungsplan();
        $form = $this->createForm(BestuhlungsplanType::class, $bestuhlungsplan)
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
            $this->handleUploadedPdf($form, $bestuhlungsplan);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($bestuhlungsplan);
            $entityManager->flush();
            return $this->redirectToRoute('bestuhlungsplan_index');
        }

        return $this->render('bestuhlungsplan/new.html.twig', [
            'bestuhlungsplan' => $bestuhlungsplan,
            'form' => $form->createView(),
        ]);
    }


    private function handleUploadedPdf(&$form, &$bestuhlungsplan)
    {
        /** @var UploadedFile $pdfFile */
        $pdfFile = $form['pdfFilePath']->getData();

        $pdfFileName = basename(tempnam($this->getParameter('pdf_file_directory'), 'stuhlplan_'));
        $pngFileName = basename(tempnam($this->getParameter('png_file_directory'), 'stuhlplan_'));

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
        $bestuhlungsplan->setPdfFilePath($pdfFileName);
        $bestuhlungsplan->setPngFilePath($pngFileName);
    }


    /**
     * @Route("/{id}", name="bestuhlungsplan_show", methods={"GET"})
     */
    public function show(Bestuhlungsplan $bestuhlungsplan): Response
    {
        return $this->render('bestuhlungsplan/show.html.twig', [
            'bestuhlungsplan' => $bestuhlungsplan,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bestuhlungsplan_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Bestuhlungsplan $bestuhlungsplan): Response
    {
        $form = $this->createForm(BestuhlungsplanType::class, $bestuhlungsplan)
            ->add('pdfFilePath', FileType::class, [
                'label' => 'Bestuhlungsplan (PDF file)',
                'mapped' => false,
                'required' => false,
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
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form['pdfFilePath']->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pdfFile) {
                // Wir löschen das alte File
                if(file_exists($oldPdfFile = $this->getParameter('pdf_file_directory') . $bestuhlungsplan->getPdfFilePath())) unlink($oldPdfFile);
                if(file_exists($oldPngFile = $this->getParameter('png_file_directory') . $bestuhlungsplan->getPngFilePath())) unlink($oldPngFile);

                $this->handleUploadedPdf($form, $bestuhlungsplan);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bestuhlungsplan_index');
        }

        return $this->render('bestuhlungsplan/edit.html.twig', [
            'bestuhlungsplan' => $bestuhlungsplan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bestuhlungsplan_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Bestuhlungsplan $bestuhlungsplan): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bestuhlungsplan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            // Dateien löschen
            $pdf_file = $this->getParameter('pdf_file_directory') . $bestuhlungsplan->getPdfFilePath();
            $png_file = $this->getParameter('png_file_directory') . $bestuhlungsplan->getPngFilePath();
            if(file_exists($pdf_file)) unlink($pdf_file);
            if(file_exists($png_file)) unlink($png_file);

            $entityManager->remove($bestuhlungsplan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('bestuhlungsplan_index');
    }
}

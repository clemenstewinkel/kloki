<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{

    /**
     * @Route("/getPng/{id}", name="getPng")
     */
    public function getPng($id)
    {
        return new BinaryFileResponse($this->getParameter('png_file_directory').$id);
    }

    /**
     * @Route("/getPdf/{id}", name="getPdf")
     */
    public function getPdf($id)
    {
        return new BinaryFileResponse($this->getParameter('pdf_file_directory').$id);
    }

}

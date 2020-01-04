<?php


namespace App\Service;

use App\Entity\KloKiEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class WordCreatorService
{
    private $twig;
    private $params;

    public function __construct(ParameterBagInterface $params, Environment $twig)
    {
        $this->twig = $twig;
        $this->params = $params;
    }

    public function createWord(KloKiEvent $event)
    {
        $source = $this->params->get('docx_directory');
        $template = $this->twig->loadTemplate('klo_ki_event/mietvertrag_document.xml.twig');
        $renderedDoc = $template->render(['e' => $event]);
        file_put_contents($source . 'word/document.xml', $renderedDoc);
        return shell_exec("cd $source; zip -rq - *");
    }
}
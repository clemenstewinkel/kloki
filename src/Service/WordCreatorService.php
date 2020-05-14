<?php /** @noinspection PhpInternalEntityUsedInspection */


namespace App\Service;

use App\Entity\KloKiEvent;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Throwable;
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
        $source = $this->params->get('odt_directory');
        try {
            $template = $this->twig->loadTemplate('klo_ki_event/mietvertrag_content.xml.twig');
        } catch (Exception $e) {
            return false;
        }
        try {
            $renderedDoc = $template->render(['e' => $event]);
        } catch (Throwable $e) {
            return false;
        }
        file_put_contents($source . 'content.xml', $renderedDoc);
        return shell_exec("cd $source; zip -rq - *");
    }
}
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
        $tempdir = trim(shell_exec('mktemp -d'));
        shell_exec("cp -r $source/* $tempdir/");

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
        file_put_contents($tempdir . '/content.xml', $renderedDoc);
        $file_content =  shell_exec("cd $tempdir; zip -rq - *");
        shell_exec("rm -rf $tempdir");
        return $file_content;
    }
}
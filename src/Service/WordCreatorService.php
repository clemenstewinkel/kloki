<?php


namespace App\Service;

use Twig\Environment;

class WordCreatorService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function createWord()
    {
        $source = '/home/clemens/test';
        return shell_exec("cd $source; zip -rq - *");
    }
}
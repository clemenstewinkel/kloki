<?php

namespace App\Command;

use App\Repository\KloKiEventRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class KlokiMailTechsCommand extends Command
{
    protected static $defaultName = 'kloki:mail-techs';

    private $mailer;
    private $userRepo;
    private $eventRepo;
    private $params;

    public function __construct(\Swift_Mailer $mailer, UserRepository $userRepo, KloKiEventRepository $eventRepo, ParameterBagInterface $params)
    {
        $this->mailer    = $mailer;
        $this->userRepo  = $userRepo;
        $this->eventRepo = $eventRepo;
        $this->params    = $params;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Send events for the next two weeks to every booked technician.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $rcpts = explode(';', $this->params->get('mailer_rcpts'));

        $technics = $this->userRepo->createQueryBuilder('u')
                ->where('u.roles LIKE :roles')
                ->setParameter('roles', '%"ROLE_TECH"%')
                ->orderBy('u.email', 'ASC')
                ->getQuery()
                ->getResult();

        print_r($technics);


/*        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('noreply@klosterkirche-lennep.de')
            ->setTo($rcpts)
            ->setBody(
                '<html><body><h1>Hier kommt ein test....</h1>Diese Mail wurde automatisch verschickt, gut ne?</body></html>',
                'text/html'
            )
        ;
        $this->mailer->send($message);
*/
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}

<?php

namespace App\Controller;

use App\DBAL\Types\ContractStateType;
use App\DBAL\Types\EventArtType;
use App\DBAL\Types\HotelStateType;
use App\DBAL\Types\PressMaterialStateType;
use App\Entity\KloKiEvent;
use App\Entity\User;
//use App\Form\AddresseType;
//use App\Form\KloKiEventEditType;
use App\Form\KloKiEventFoodType;
use App\Form\KloKiEventRemarkType;
use App\Form\KloKiEventType;
use App\Repository\KloKiEventRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use App\Service\WordCreatorService;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
//use Symfony\Component\Form\FormEvent;
//use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * @Route("/event")
 */
class KloKiEventController extends AbstractController
{
    /**
     * @Route("/", name="klo_ki_event_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_FOOD')")
     * @param KloKiEventRepository $kloKiEventRepository
     * @param PaginatorInterface $paginator
     * @param RoomRepository $roomRepo
     * @param Request $request
     * @return Response
     */
    public function index(KloKiEventRepository $kloKiEventRepository, PaginatorInterface $paginator, RoomRepository $roomRepo, Request $request): Response
    {
        $query = $kloKiEventRepository->getQueryFromRequest($request);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('pp', 10)
        );
        return $this->render('klo_ki_event/index.html.twig', [
            'pagination' => $pagination,
            'rooms'      => $roomRepo->findAll()
        ]);
    }

    /**
     * @Route("/helper/index", name="klo_ki_event_index_helper", methods={"GET"})
     * @IsGranted("ROLE_HELPER")
     * @param KloKiEventRepository $kloKiEventRepository
     * @return Response
     */
    public function index_helper(KloKiEventRepository $kloKiEventRepository): Response
    {
        $events = $kloKiEventRepository->createQueryBuilder('e')
            ->andWhere('e.helperRequired = 1')
            ->andWhere('e.start >= CURRENT_DATE()')
            ->orderBy('e.start')
            ->getQuery()
            ->getResult();
        return $this->render('klo_ki_event/index_helper.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * @Route("/helper/duties", name="klo_ki_event_duties_helper", methods={"GET"})
     * @IsGranted("ROLE_HELPER")
     * @param KloKiEventRepository $kloKiEventRepository
     * @return Response
     */
    public function duties_helper(KloKiEventRepository $kloKiEventRepository): Response
    {
        $user_id = $this->getUser()->getId();
        $events = $kloKiEventRepository->createQueryBuilder('e')
            ->andWhere('e.helperRequired = 1')
            ->andWhere('e.start >= CURRENT_DATE()')
            ->andWhere("e.helperKasse        = $user_id OR 
                        e.helperEinlassEins  = $user_id OR 
                        e.helperEinlassZwei  = $user_id OR 
                        e.helperSpringerEins = $user_id OR 
                        e.helperSpringerZwei = $user_id OR 
                        e.helperGarderobe    = $user_id")
            ->orderBy('e.start')
            ->getQuery()
            ->getResult();
        return $this->render('klo_ki_event/duties_helper.html.twig', [
            'events' => $events
        ]);
    }


    /**
     * @Route("/dutyMails", name="klo_ki_event_duty_mails", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param KloKiEventRepository $eventRepo
     * @param UserRepository $userRepo
     * @param Request $request
     * @param SendMailService $mailer
     * @return Response
     */
    public function dutyMails(KloKiEventRepository $eventRepo, UserRepository $userRepo, Request $request, SendMailService $mailer): Response
    {
        if($request->isMethod("POST"))
        {
            if($last_day = $request->request->get('helperDutiesUntilDay'))
            {
                $allHelpers = $userRepo->createQueryBuilder('u')
                    ->where('u.roles LIKE :roles')
                    ->setParameter('roles', '%"ROLE_HELPER"%')
                    ->orderBy('u.email', 'ASC')->getQuery()->getResult();

                $mailCount = 0;
                foreach($allHelpers as $helper)
                {
                    /** @var $helper User */
                    $user_id = $helper->getId();
                    $events = $eventRepo->createQueryBuilder('e')
                        ->andWhere('e.helperRequired = 1')
                        ->andWhere('e.start >= CURRENT_DATE()')
                        ->andWhere("e.helperKasse    = $user_id OR 
                        e.helperEinlassEins  = $user_id OR 
                        e.helperEinlassZwei  = $user_id OR 
                        e.helperSpringerEins = $user_id OR 
                        e.helperSpringerZwei = $user_id OR 
                        e.helperGarderobe    = $user_id")
                        ->orderBy('e.start')
                        ->getQuery()
                        ->getResult();
                    if(count($events) > 0)
                    {
                        if($mailer->sendDutyListToHelper($last_day, $events, $helper, $request->request->get('additionalText')))
                        {
                            $mailCount++;
                        }
                    }
                }
                $this->addFlash('success', "Es wurden $mailCount Mails versendet. Dienstpläne bis $last_day, addText: " . $request->request->get('additionalText'));
            }
        }

        return $this->render('klo_ki_event/duty_mailer.html.twig', [
        ]);
    }


    /**
     * @Route("/dispo", name="klo_ki_event_dispo", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_FOOD')")
     * @param KloKiEventRepository $eventRepo
     * @param Request $request
     * @return Response
     */
    public function dispo(KloKiEventRepository $eventRepo, Request $request): Response
    {
        $events = ($day = $request->query->get('dispoForDay'))?
            $eventRepo->createQueryBuilder('event')
            ->andWhere('event.start > :day')->setParameter('day', $day)
            ->andWhere('event.start < :dayEnd')->setParameter('dayEnd', $day . ' 23:59:59')
            ->getQuery()->getResult():null;

        return $this->render('klo_ki_event/dispo.html.twig', [
            'pagination' => $events,
        ]);
    }


    /**
     * @Route("/calendar", name="event_calendar", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @param KloKiEventRepository $eventRepository
     * @return Response
     */
    public function calendar(KloKiEventRepository $eventRepository)
    {
        return $this->render('klo_ki_event/calendar.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    /**
     * @Route("/resize", name="klo_ki_event_resize", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param KloKiEventRepository $eventRepo
     * @return Response
     * @throws Exception
     */
    public function resize(Request $request, KloKiEventRepository $eventRepo)
    {
        $data = json_decode($request->getContent(), true);
        $event = $eventRepo->findOneBy(['id' => $data['id']]);
        if($event)
        {
            $event->set_FC_end(new DateTime($data['enddate']));
            $this->getDoctrine()->getManager()->flush();
            return new Response('OK');
        }
        return new Response('NO_SUCH_EVENT', 400);
    }

    /**
     * @Route("/replace", name="klo_ki_event_replace", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param KloKiEventRepository $eventRepo
     * @param RoomRepository $roomRepo
     * @return Response
     * @throws Exception
     */
    public function replace(Request $request, KloKiEventRepository $eventRepo, RoomRepository $roomRepo)
    {
        $data = json_decode($request->getContent(), true);
        $event = $eventRepo->findOneBy(['id' => $data['id']]);
        if($event)
        {
            $event->setAllDay($data['allDay']);
            $event->setStart(new DateTime($data['startdate']));
            $event->set_FC_End(new DateTime($data['enddate']));
            if(isset($data['newResource']))
            {
                $newRoom = $roomRepo->findOneBy(['id' => $data['newResource']]);
                if($newRoom) $event->setRoom($newRoom);
            }
            $this->getDoctrine()->getManager()->flush();
            return new Response('OK');
        }
        return new Response('NO_SUCH_EVENT', 400);
    }


    /**
     * @Route("/new", name="klo_ki_event_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param KloKiEventRepository $eventRepo
     * @return Response
     */
    public function new(Request $request, KloKiEventRepository $eventRepo): Response
    {
        $kloKiEvent = new KloKiEvent();

        // Wenn wir in der URL den Parameter parentIdForNewChild haben, übernehmen wir
        // einige Werte aus dem Mutter-Element
        if($parentId = $request->query->getInt('parentIdForNewChild'))
        {
            $parentEvent = $eventRepo->findOneBy(['id' => $parentId]);
            if($parentEvent)
            {
                $kloKiEvent->setStart($parentEvent->getStart());
                $kloKiEvent->setEnd($parentEvent->getEnd());
                $kloKiEvent->setAllDay($parentEvent->getAllDay());
                $kloKiEvent->setName($parentEvent->getName() . ' (zus.)');
                $kloKiEvent->setParentEvent($parentEvent);
                $kloKiEvent->setKontakt($parentEvent->getKontakt());
                $kloKiEvent->setKategorie($parentEvent->getKategorie());
                $kloKiEvent->setArt($parentEvent->getArt());
            }
        }

        $form = $this->createForm(KloKiEventType::class, $kloKiEvent);
        $formTemplate = 'klo_ki_event/_form.html.twig';

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($kloKiEvent);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->render('klo_ki_event/_show.html.twig', [
                    'klo_ki_event' => $kloKiEvent,
                ]);
            }

            return $this->redirectToRoute('klo_ki_event_index');
        }

        // Wenn die Anfrage über AJAX kam, rendern wir nur das Formular!
        if ($request->isXmlHttpRequest()) {
            return $this->render($formTemplate, [
                'klo_ki_event' => $kloKiEvent,
                'klo_ki_form_action' => $this->generateUrl('klo_ki_event_new'),
                'form' => $form->createView()
            ]);
        }

        return $this->render('klo_ki_event/new.html.twig', [
            'klo_ki_event' => $kloKiEvent,
            'form_template' => $formTemplate,
            'klo_ki_form_action' => $this->generateUrl('klo_ki_event_new'),
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/newfood", name="klo_ki_event_new_food", methods={"GET","POST"})
     * @IsGranted("ROLE_FOOD")
     * @param Request $request
     * @param KloKiEventRepository $eventRepo
     * @param SendMailService $mailService
     * @return Response
     */
    public function newfood(Request $request, KloKiEventRepository $eventRepo, SendMailService $mailService): Response
    {
        $kloKiEvent = new KloKiEvent();

        // Wenn wir in der URL den Parameter parentIdForNewChild haben, übernehmen wir
        // einige Werte aus dem Mutter-Element
        if($parentId = $request->query->getInt('parentIdForNewChild'))
        {
            $parentEvent = $eventRepo->findOneBy(['id' => $parentId]);
            if($parentEvent)
            {
                $kloKiEvent->setStart($parentEvent->getStart());
                $kloKiEvent->setEnd($parentEvent->getEnd());
                $kloKiEvent->setAllDay($parentEvent->getAllDay());
                $kloKiEvent->setName($parentEvent->getName() . ' (zus.)');
                $kloKiEvent->setParentEvent($parentEvent);
                $kloKiEvent->setKontakt($parentEvent->getKontakt());
                $kloKiEvent->setKategorie($parentEvent->getKategorie());
                $kloKiEvent->setArt($parentEvent->getArt());
            }
        }

        // Wenn wir in der URL den Parameter id2Copy haben,
        // kopieren wir fast alle Werte aus dem übergebenen Event
        if($id2Copy = $request->query->getInt('id2Copy'))
        {
            $event2Copy = $eventRepo->findOneBy(['id' => $id2Copy]);
            if($event2Copy)
            {
                $kloKiEvent->setStart(      $event2Copy->getStart());
                $kloKiEvent->setEnd(        $event2Copy->getEnd());
                $kloKiEvent->setAllDay(     $event2Copy->getAllDay());
                $kloKiEvent->setName(       $event2Copy->getName() );
                $kloKiEvent->setParentEvent($event2Copy->getParentEvent());
                $kloKiEvent->setKontakt(    $event2Copy->getKontakt());
                $kloKiEvent->setKategorie(  $event2Copy->getKategorie());
                $kloKiEvent->setArt(        $event2Copy->getArt());
                foreach($event2Copy->getAusstattung() as $a) $kloKiEvent->addAusstattung($a);
            }
        }

        $form = $this->get('form.factory')->createNamed('klo_ki_event', KloKiEventFoodType::class, $kloKiEvent);
        $kloKiEvent->setIsFixed(false);
        $kloKiEvent->setArt(EventArtType::RENTAL);
        $kloKiEvent->setHotelState(HotelStateType::NONE);
        $kloKiEvent->setPressMaterialState(PressMaterialStateType::NONE);
        $kloKiEvent->setGemaListState(PressMaterialStateType::NONE);
        $formTemplate = 'klo_ki_event/_form_food.html.twig';

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($kloKiEvent);
            $entityManager->flush();
            if($kloKiEvent->getContractState() == 'requested') $mailService->informAboutContractRequest($kloKiEvent);
            if ($request->isXmlHttpRequest()) {
                return $this->render('klo_ki_event/_show.html.twig', [
                    'klo_ki_event' => $kloKiEvent,
                ]);
            }
            return $this->redirectToRoute('klo_ki_event_index');
        }

        // Wenn die Anfrage über AJAX kam, rendern wir nur das Formular!
        if ($request->isXmlHttpRequest()) {
            return $this->render($formTemplate, [
                'klo_ki_event' => $kloKiEvent,
                'klo_ki_form_action' => $this->generateUrl('klo_ki_event_new_food'),
                'form' => $form->createView()
            ]);
        }

        return $this->render('klo_ki_event/new.html.twig', [
            'klo_ki_event' => $kloKiEvent,
            'form_template' => $formTemplate,
            'klo_ki_form_action' => $this->generateUrl('klo_ki_event_new'),
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/icanhelp/{id}", name="klo_ki_event_icanhelp", methods={"POST"})
     * @IsGranted("ROLE_HELPER")
     * @param Request $request
     * @param KloKiEvent $kloKiEvent
     * @return RedirectResponse|Response
     */
    public function sayICanHelp(Request $request, KloKiEvent $kloKiEvent)
    {
        if ($this->isCsrfTokenValid('icanhelp'.$kloKiEvent->getId(), $request->request->get('_token'))) {
            $kloKiEvent->addAvailableHelper($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('klo_ki_event/_show_helper.html.twig', [
                'klo_ki_event' => $kloKiEvent,
                'userInHelpers' => $this->isInAvailableHelpers($kloKiEvent)
            ]);
        }
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/icannothelp/{id}", name="klo_ki_event_icannothelp", methods={"POST"})
     * @IsGranted("ROLE_HELPER")
     * @param Request $request
     * @param KloKiEvent $kloKiEvent
     * @param SendMailService $mailer
     * @return RedirectResponse|Response
     */
    public function sayICanNotHelp(Request $request, KloKiEvent $kloKiEvent, SendMailService $mailer)
    {
        if ($this->isCsrfTokenValid('icannothelp'.$kloKiEvent->getId(), $request->request->get('_token'))) {
            $kloKiEvent->removeAvailableHelper($this->getUser());
            // Wenn der User schon eingeteilt war, informieren wir das Büro über die Absage:
            if( $kloKiEvent->getHelperEinlassEins()  == $this->getUser() or
                $kloKiEvent->getHelperEinlassZwei()  == $this->getUser() or
                $kloKiEvent->getHelperSpringerEins() == $this->getUser() or
                $kloKiEvent->getHelperSpringerZwei() == $this->getUser() or
                $kloKiEvent->getHelperKasse()        == $this->getUser() or
                $kloKiEvent->getHelperGarderobe()    == $this->getUser()
            )
            $mailer->informAboutHelperCancelled($kloKiEvent, $this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('klo_ki_event/_show_helper.html.twig', [
                'klo_ki_event' => $kloKiEvent,
                'userInHelpers' => $this->isInAvailableHelpers($kloKiEvent)
            ]);
        }
        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Route("/eventRange", name="klo_ki_event_json", methods={"GET"})
     * @param SerializerInterface $serializer
     * @param KloKiEventRepository $repo
     * @param Request $request
     * @return JsonResponse
     */
    public function getEventRange(SerializerInterface $serializer, KloKiEventRepository $repo, Request $request) : JsonResponse
    {
        $start = $request->query->get('start');
        $end   = $request->query->get('end');

        $start = date('Y-m-d H:i:s', strtotime($start) - (30*24*60*60));
        $end = date('Y-m-d H:i:s', strtotime($end) + (30*24*60*60));

        $events = $repo->createQueryBuilder('event')
            ->andWhere('event.start >= :start OR event.end > :start')
            ->andWhere('event.start <= :end')
            ->setParameter(':start', $start)
            ->setParameter(':end', $end)
            ->getQuery()->getResult();
        $json = $serializer->serialize($events, 'json', ['groups' => 'events:read']);

        return new JsonResponse($json, 200, [], true);
    }

    private function isInAvailableHelpers(KloKiEvent $kloKiEvent)
    {
        $userInAvailabelHelpers = false;
        $myId = $this->getUser()->getId();
        foreach($kloKiEvent->getAvailableHelpers() as $ah)
        {
            if ($myId === $ah->getId())
            {
                $userInAvailabelHelpers = true;
                break;
            }
        }
        return $userInAvailabelHelpers;
    }

    /**
     * @Route("/showHelper/{id}", name="klo_ki_event_show_helper", methods={"GET"})
     * @IsGranted("ROLE_HELPER")
     * @param KloKiEvent $kloKiEvent
     * @param Request $request
     * @return Response
     */
    public function showHelper(KloKiEvent $kloKiEvent, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('klo_ki_event/_show_helper.html.twig', [
                'klo_ki_event' => $kloKiEvent,
                'userInHelpers' => $this->isInAvailableHelpers($kloKiEvent)
            ]);
        }
        return $this->render('klo_ki_event/show.html.twig', [
            'klo_ki_event' => $kloKiEvent,
            'userInHelpers' => $this->isInAvailableHelpers($kloKiEvent)
        ]);
    }


    /**
     * @Route("/show/{id}", name="klo_ki_event_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_FOOD')")
     * @param KloKiEvent $kloKiEvent
     * @param Request $request
     * @return Response
     */
    public function show(KloKiEvent $kloKiEvent, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            return $this->render('klo_ki_event/_show.html.twig', [
                'klo_ki_event' => $kloKiEvent,
                'userInHelpers' => $this->isInAvailableHelpers($kloKiEvent)
            ]);
        }
        return $this->render('klo_ki_event/show.html.twig', [
            'klo_ki_event' => $kloKiEvent,
            'userInHelpers' => $this->isInAvailableHelpers($kloKiEvent)
        ]);
    }

    /**
     * @Route("/createWord/{id}", name="klo_ki_event_create_word", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param KloKiEventRepository $klokiRepo
     * @param WordCreatorService $wService
     * @param KloKiEvent $kloKiEvent
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function create_word(KloKiEventRepository $klokiRepo, WordCreatorService $wService, KloKiEvent $kloKiEvent): Response
    {
        // Hat dieses Event schon eine Vertrags-Nummer?
        if($kloKiEvent->getContractNumber())
        // Dann zählen wir die Revisions-Nummer eins hoch
        {
            $kloKiEvent->setContractRevision($kloKiEvent->getContractRevision() + 1);
        }
        else
        {
            // Hat das Event dagegen noch keine Vertrags-/Revisions-Nummer
            // Dann suchen wir in der Datenbank nach der höchsten Vertragsnummer
            // erhöhen um eins und setzen die Revisions-Nummer auf 1.
            $highest_contract_number = $klokiRepo->createQueryBuilder('e')
                ->select('MAX(e.contractNumber)')
                ->getQuery()
                ->getSingleScalarResult();
            $kloKiEvent->setContractNumber($highest_contract_number ? ($highest_contract_number + 1) : 1);
            $kloKiEvent->setContractRevision(1);
        }


        // In jedem Fall schreiben wir die neue Vertrags-/Revisions-Nummer in die Datenbank
        $this->getDoctrine()->getManager()->flush();

        $response = new Response($wService->createWord($kloKiEvent));
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'V_'.$kloKiEvent->getContractNumber().'-'
            .$kloKiEvent->getContractRevision().'-'
            .$kloKiEvent->getStart()->format('Ymd').'-'
            .
            (
                $kloKiEvent->getKontakt()->getFirma()?
                    iconv("utf-8","ascii//TRANSLIT",$kloKiEvent->getKontakt()->getFirma()):
                    iconv("utf-8","ascii//TRANSLIT",$kloKiEvent->getKontakt()->getNachname())
            ) . '.odt'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/octet-stream');
        return $response;
    }


    /**
     * @Route("/{id}/edit", name="klo_ki_event_edit", methods={"GET","POST"})
     * @IsGranted({"ROLE_ADMIN", "ROLE_FOOD"})
     * @IsGranted("EVENT_EDIT", subject="kloKiEvent")
     * @param Request $request
     * @param KloKiEvent $kloKiEvent
     * @param SendMailService $mailService
     * @return Response
     */
    public function edit(Request $request, KloKiEvent $kloKiEvent, SendMailService $mailService): Response
    {
        // Wenn wir keine Admin sind, dürfen wir nur optionale Events anlegen!
        if ($this->isGranted("ROLE_ADMIN"))
        {
            $form = $this->createForm(KloKiEventType::class, $kloKiEvent);
            $formTemplate = 'klo_ki_event/_form.html.twig';
        }
        else // Role: Food!
        {
            $form = $this->get('form.factory')->createNamed('klo_ki_event', KloKiEventFoodType::class, $kloKiEvent);
            if(in_array($kloKiEvent->getContractState(), [ContractStateType::SENT, ContractStateType::RECEIVED]))
            {
                $form->remove('contractState');
            }
            $formTemplate = 'klo_ki_event/_form_food.html.twig';
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            // Wurde der Vertrags-Status auf "requested" gesetzt? Dann Mail-Versand
            $uow = $entityManager->getUnitOfWork();
            $uow->computeChangeSets();
            $changeSet = $uow->getEntityChangeSet($kloKiEvent);
            if(isset($changeSet['contractState']) and $changeSet['contractState'][1] == 'requested')
            {
                $mailService->informAboutContractRequest($kloKiEvent);
            }

            $entityManager->persist($kloKiEvent->getKontakt());
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->render('klo_ki_event/_show.html.twig', [
                    'klo_ki_event' => $kloKiEvent,
                ]);
            }
            return $this->redirectToRoute('klo_ki_event_index');
        }

        // Wenn die Anfrage über AJAX kam, rendern wir nur das Formular!
         if ($request->isXmlHttpRequest()) {
            return $this->render($formTemplate, [
                'klo_ki_event' => $kloKiEvent,
                'klo_ki_form_action' => $this->generateUrl('klo_ki_event_edit', ['id' => $kloKiEvent->getId()]),
                'form' => $form->createView()
            ]);
        }

        return $this->render('klo_ki_event/edit.html.twig', [
            'klo_ki_event' => $kloKiEvent,
            'form_template' => $formTemplate,
            'klo_ki_form_action' => $this->generateUrl('klo_ki_event_edit', ['id' => $kloKiEvent->getId()]),
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/editRemark", name="klo_ki_event_edit_remark", methods={"GET","POST"})
     * @IsGranted({"ROLE_FOOD", "ROLE_ADMIN"})
     * @param Request $request
     * @param KloKiEvent $kloKiEvent
     * @param SendMailService $mailService
     * @return Response
     */
    public function editRemark(Request $request, KloKiEvent $kloKiEvent, SendMailService $mailService): Response
    {
        $form = $this->get('form.factory')->createNamed('klo_ki_event', KloKiEventRemarkType::class, $kloKiEvent);
        $form->handleRequest($request);
        $formTemplate = 'klo_ki_event/_form_remark.html.twig';

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->render('klo_ki_event/_show.html.twig', [
                    'klo_ki_event' => $kloKiEvent,
                ]);
            }
            return $this->redirectToRoute('klo_ki_event_index');
        }

        // Wenn die Anfrage über AJAX kam, rendern wir nur das Formular!
        if ($request->isXmlHttpRequest()) {
            return $this->render($formTemplate, [
                'klo_ki_event' => $kloKiEvent,
                'klo_ki_form_action' => $this->generateUrl('klo_ki_event_edit_remark', ['id' => $kloKiEvent->getId()]),
                'form' => $form->createView()
            ]);
        }

        return $this->render('klo_ki_event/edit.html.twig', [
            'klo_ki_event' => $kloKiEvent,
            'form_template' => $formTemplate,
            'klo_ki_form_action' => $this->generateUrl('klo_ki_event_edit_remark', ['id' => $kloKiEvent->getId()]),
            'form' => $form->createView(),
        ]);
    }






    /**
     * @Route("/{id}/delete", name="klo_ki_event_json_delete", methods={"DELETE"})
     * @IsGranted({"ROLE_ADMIN", "ROLE_FOOD"})
     * @IsGranted("EVENT_DELETE", subject="kloKiEvent")
     * @param KloKiEvent $kloKiEvent
     * @return Response
     */
    public function json_delete(KloKiEvent $kloKiEvent): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($kloKiEvent);
        $entityManager->flush();
        return new Response('OK');
    }


    /**
     * @Route("/{id}", name="klo_ki_event_delete", methods={"DELETE"})
     * @IsGranted({"ROLE_ADMIN", "ROLE_FOOD"})
     * @IsGranted("EVENT_DELETE", subject="kloKiEvent")
     * @param Request $request
     * @param KloKiEvent $kloKiEvent
     * @return Response
     */
    public function delete(Request $request, KloKiEvent $kloKiEvent): Response
    {
        if ($this->isCsrfTokenValid('delete'.$kloKiEvent->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($kloKiEvent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('klo_ki_event_index');
    }


    /**
     * @Route("/{id}/sendHelperMail", name="klo_ki_event_send_helper_mail", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param KloKiEvent $event
     * @param SendMailService $mailer
     * @return JsonResponse
     */
    public function send_helper_mail(Request $request, KloKiEvent $event, SendMailService $mailer): JsonResponse
    {
        $rcptsCount = $mailer->informHelpersAboutEvent($event, $request->request->get('message'));
        return new JsonResponse(['sent' => $rcptsCount]);
    }

    /**
     * @Route("/{id}/sendTechMail", name="klo_ki_event_send_tech_mail", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param KloKiEvent $event
     * @param SendMailService $mailer
     * @return JsonResponse
     */
    public function send_tech_mail(Request $request, KloKiEvent $event, SendMailService $mailer): JsonResponse
    {
        $rcptsCount = $mailer->informTechsAboutEvent($event, $request->request->get('message'));
        return new JsonResponse(['sent' => $rcptsCount]);
    }

}

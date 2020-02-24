<?php

namespace App\Controller;

use App\DBAL\Types\ContractStateType;
use App\DBAL\Types\EventArtType;
use App\DBAL\Types\HotelStateType;
use App\DBAL\Types\PressMaterialStateType;
use App\Entity\KloKiEvent;
use App\Form\AddresseType;
use App\Form\KloKiEventEditType;
use App\Form\KloKiEventFoodType;
use App\Form\KloKiEventType;
use App\Repository\KloKiEventRepository;
use App\Repository\RoomRepository;
use App\Service\WordCreatorService;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/event")
 */
class KloKiEventController extends AbstractController
{
    /**
     * @Route("/", name="klo_ki_event_index", methods={"GET"})
     * @IsGranted({"ROLE_ADMIN", "ROLE_FOOD"})
     */
    public function index(KloKiEventRepository $kloKiEventRepository, PaginatorInterface $paginator, RoomRepository $roomRepo, Request $request): Response
    {
        $query = $kloKiEventRepository->getQueryFromRequest($request);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('klo_ki_event/index.html.twig', [
            'pagination' => $pagination,
            'rooms'      => $roomRepo->findAll()
        ]);
    }

    /**
     * @Route("/helper/index", name="klo_ki_event_index_helper", methods={"GET"})
     * @IsGranted({"ROLE_HELPER"})
     */
    public function index_helper(KloKiEventRepository $kloKiEventRepository): Response
    {
        $events = $kloKiEventRepository->createQueryBuilder('e')
            ->andWhere('e.helperRequired = 1')
            ->andWhere('e.start > CURRENT_DATE()')
            ->orderBy('e.start')
            ->getQuery()
            ->getResult();
        return $this->render('klo_ki_event/index_helper.html.twig', [
            'events' => $events
        ]);
    }



    /**
     * @Route("/dispo", name="klo_ki_event_dispo", methods={"GET"})
     * @IsGranted({"ROLE_ADMIN"})
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
     */
    public function calendar(KloKiEventRepository $eventRepository)
    {
        return $this->render('klo_ki_event/calendar.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    /**
     * @Route("/resize", name="klo_ki_event_resize", methods={"POST"})
     * @IsGranted({"ROLE_ADMIN"})
     */
    public function resize(Request $request, KloKiEventRepository $eventRepo)
    {
        $data = json_decode($request->getContent(), true);
        $event = $eventRepo->findOneBy(['id' => $data['id']]);
        if($event)
        {
            $event->set_FC_end(new \DateTime($data['enddate']));
            $this->getDoctrine()->getManager()->flush();
            return new Response('OK');
        }
        return new Response('NO_SUCH_EVENT', 400);
    }

    /**
     * @Route("/replace", name="klo_ki_event_replace", methods={"POST"})
     * @IsGranted({"ROLE_ADMIN"})
     */
    public function replace(Request $request, KloKiEventRepository $eventRepo, RoomRepository $roomRepo)
    {
        $data = json_decode($request->getContent(), true);
        $event = $eventRepo->findOneBy(['id' => $data['id']]);
        if($event)
        {
            $event->setAllDay($data['allDay']);
            $event->setStart(new \DateTime($data['startdate']));
            $event->set_FC_End(new \DateTime($data['enddate']));
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
     * @IsGranted({"ROLE_ADMIN"})
     */
    public function new(LoggerInterface $logger, Request $request, KloKiEventRepository $eventRepo): Response
    {
        $kloKiEvent = new KloKiEvent();

        // Wenn wir in der URL den Paremeter parentIdForNewChild haben, übernehmen wir
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
     * @IsGranted({"ROLE_FOOD"})
     */
    public function newfood(LoggerInterface $logger, Request $request, KloKiEventRepository $eventRepo): Response
    {
        $kloKiEvent = new KloKiEvent();

        // Wenn wir in der URL den Paremeter parentIdForNewChild haben, übernehmen wir
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
     * @Route("/icanhelp/{id}", name="klo_ki_event_icanhelp", methods={"POST"})
     * @IsGranted({"ROLE_HELPER"})
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
        return $this->redirectToRoute('klo_ki_event_index_helper');
    }

    /**
     * @Route("/icannothelp/{id}", name="klo_ki_event_icannothelp", methods={"POST"})
     * @IsGranted({"ROLE_HELPER"})
     */
    public function sayICanNotHelp(Request $request, KloKiEvent $kloKiEvent)
    {
        if ($this->isCsrfTokenValid('icannothelp'.$kloKiEvent->getId(), $request->request->get('_token'))) {
            $kloKiEvent->removeAvailableHelper($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('klo_ki_event/_show_helper.html.twig', [
                'klo_ki_event' => $kloKiEvent,
                'userInHelpers' => $this->isInAvailableHelpers($kloKiEvent)
            ]);
        }
        return $this->redirectToRoute('klo_ki_event_index_helper');
    }



    /**
     * @Route("/eventRange", name="klo_ki_event_json", methods={"GET"})
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
     * @IsGranted({"ROLE_HELPER"})
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
     * @IsGranted({"ROLE_ADMIN", "ROLE_FOOD"})
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
     * @IsGranted({"ROLE_ADMIN"})
     */
    public function create_word(WordCreatorService $wService, KloKiEvent $kloKiEvent): Response
    {
        $response = new Response($wService->createWord($kloKiEvent));
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'MietVertrag_'.$kloKiEvent->getContractNumber().'.docx'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'application/octet-stream');
        return $response;
    }


    /**
     * @Route("/{id}/edit", name="klo_ki_event_edit", methods={"GET","POST"})
     * @IsGranted({"ROLE_ADMIN", "ROLE_FOOD"})
     */
    public function edit(Request $request, KloKiEvent $kloKiEvent): Response
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
     * @Route("/{id}/delete", name="klo_ki_event_json_delete", methods={"DELETE"})
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
}

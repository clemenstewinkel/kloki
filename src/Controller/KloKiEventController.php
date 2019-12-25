<?php

namespace App\Controller;

use App\Entity\KloKiEvent;
use App\Form\AddresseType;
use App\Form\KloKiEventEditType;
use App\Form\KloKiEventType;
use App\Repository\KloKiEventRepository;
use App\Repository\RoomRepository;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
     */
    public function index(KloKiEventRepository $kloKiEventRepository, PaginatorInterface $paginator, RoomRepository $roomRepo, Request $request): Response
    {
        $querybuilder = $kloKiEventRepository->createQueryBuilder('event')->innerJoin('event.room', 'room');
        if($request->query->get('room_id'))
            $querybuilder->andWhere('room.id IN (:roomIds)')->setParameter('roomIds', $request->query->get('room_id'));

        if($beginAtAfter = $request->query->get('beginAtAfter'))
            $querybuilder->andWhere('event.beginAt > :beginAtAfter')->setParameter('beginAtAfter', $beginAtAfter);

        if($beginAtBefore = $request->query->get('beginAtBefore'))
            $querybuilder->andWhere('event.beginAt < :beginAtBefore')->setParameter('beginAtBefore', $beginAtBefore . ' 23:59:59');

        if($nameContains = $request->query->get('name_contains'))
            $querybuilder->andWhere('event.name LIKE :nameContains')->setParameter('nameContains', '%' . $nameContains . '%');


        $query = $querybuilder->getQuery();

        dump($request->query->get('room_id'));
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
     * @IsGranted({"ROLE_ADMIN", "ROLE_FOOD"})
     */
    public function new(LoggerInterface $logger, Request $request): Response
    {
        $logger->debug('KLOKI: new called in EventController');
        $kloKiEvent = new KloKiEvent();
        $form = $this->createForm(KloKiEventType::class, $kloKiEvent);

        // Wenn wir keine Admin sind, dürfen wir nur optionale Events anlegen!
        if (!$this->isGranted("ROLE_ADMIN"))
        {
            $form->remove('isFixed');
            $kloKiEvent->setIsFixed(false);
        }

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
            return $this->render('klo_ki_event/_form.html.twig', [
                'klo_ki_event' => $kloKiEvent,
                'klo_ki_form_action' => '/event/new',
                'form' => $form->createView()
            ]);
        }

        return $this->render('klo_ki_event/new.html.twig', [
            'klo_ki_event' => $kloKiEvent,
            'klo_ki_form_action' => '/event/new',
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
     * @Route("/show/{id}", name="klo_ki_event_show", methods={"GET"})
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
     * @Route("/{id}/edit", name="klo_ki_event_edit", methods={"GET","POST"})
     * @IsGranted({"ROLE_ADMIN", "ROLE_FOOD"})
     */
    public function edit(Request $request, KloKiEvent $kloKiEvent): Response
    {
        $form = $this->createForm(KloKiEventType::class, $kloKiEvent);
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
            return $this->render('klo_ki_event/_form.html.twig', [
                'klo_ki_event' => $kloKiEvent,
                'klo_ki_form_action' => '/event/' . $kloKiEvent->getId() . '/edit',
                'form' => $form->createView()
            ]);
        }

        return $this->render('klo_ki_event/edit.html.twig', [
            'klo_ki_event' => $kloKiEvent,
            'klo_ki_form_action' => '/event/' . $kloKiEvent->getId() . '/edit',
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

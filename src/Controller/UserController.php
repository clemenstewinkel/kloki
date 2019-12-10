<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePwType;
use App\Form\ChangeMyPwType;
use App\Form\UserEditType;
use App\Form\UserNewType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserNewType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form['plainPassword']->getData()));
            $entityManager->persist($user);
            $this->addFlash('success', "Der User wurde angelegt.");
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/show/{id}", name="user_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="user_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, User $user): Response
    {
        $user->setPlainPassword('dummy123');
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        //$form['plainPassword']->setData('dummy123');

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Benutzer wurde aktualisiert.");

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/changePw/{id}", name="user_pw_change", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function change_pw(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangePwType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $form['plainPassword']->getData()));
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "Passwort wurde geändert.");
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/changePw.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/changeMyPw", name="user_mypw_change", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function change_mypw(UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangeMyPwType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($this->passwordEncoder->isPasswordValid($user, $form['oldPassword']->getData()))
            {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $form['plainPassword']->getData()));
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', "Passwort wurde geändert.");
                return $this->redirectToRoute('app_homepage');
            }
            else
            {
                $form['oldPassword']->addError(new FormError('Das Passwort stimmt nicht!'));
            }
        }

        return $this->render('user/changeMyPw.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', "Der User wurde gelöscht");
        }

        return $this->redirectToRoute('user_index');
    }
}

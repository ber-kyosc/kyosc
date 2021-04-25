<?php

namespace App\Controller;

use App\Entity\Clan;
use App\Entity\Message;
use App\Entity\User;
use App\Form\ClanType;
use App\Form\MessageType;
use App\Repository\ClanRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/clan", name="clan_")
 */
class ClanController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @param ClanRepository $clanRepository
     * @return Response
     */
    public function index(ClanRepository $clanRepository): Response
    {
        return $this->render('clan/index.html.twig', [
            'clans' => $clanRepository->findAll(),
        ]);
    }

    /**
     * @Route("/nouveau", name="new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $clan = new Clan();
        $form = $this->createForm(ClanType::class, $clan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $clan->setCreatedAt(new DateTime());
            $clan->setUpdatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $clan->setCreator($this->getUser());
            /* @phpstan-ignore-next-line */
            $clan->addMember($this->getUser());
            $entityManager->persist($clan);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Votre clan a bien été créé !"
            );

            return $this->redirectToRoute('clan_index');
        }

        return $this->render('clan/new.html.twig', [
            'clan' => $clan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET","POST"})
     */
    public function show(Clan $clan, Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        /* @phpstan-ignore-next-line */
        if ($form->isSubmitted() && $form->isValid() && $form->get('save-message')->isClicked()) {
            $entityManager = $this->getDoctrine()->getManager();
            $message->setClan($clan);
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $message->setCreatedAt(new DateTime());
            $message->setUpdatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $message->setAuthor($this->getUser());
            $message->setIsPublic(true);
            $entityManager->persist($message);
            $entityManager->flush();
        }
        return $this->render('clan/show.html.twig', [
            'clan' => $clan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/mon-clan", name="my-clan", methods={"GET","POST"})
     */
    public function myClan(Clan $clan, Request $request): Response
    {
        $membersId = [];
        foreach ($clan->getMembers() as $member) {
            $membersId[] = $member->getId();
        }

        /* @phpstan-ignore-next-line */
        if (!($this->getUser() == $clan->getCreator() || in_array($this->getUser()->getId(), $membersId))) {
            throw new AccessDeniedException('Seul un membre du clan "'
                . $clan->getName()
                . '" peut acceder à cet espace');
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        /* @phpstan-ignore-next-line */
        if ($form->isSubmitted() && $form->isValid() && $form->get('save-message')->isClicked()) {
            $entityManager = $this->getDoctrine()->getManager();
            $message->setClan($clan);
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $message->setCreatedAt(new DateTime());
            $message->setUpdatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $message->setAuthor($this->getUser());
            $message->setIsPublic(false);
            $entityManager->persist($message);
            $entityManager->flush();
        }

        return $this->render('clan/my-clan.html.twig', [
            'clan' => $clan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/{id}/edition",
     *     name="edit",
     *     methods={"GET", "POST", "PUT"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param Clan $clan
     * @return Response
     */
    public function edit(Request $request, Clan $clan): Response
    {
        if (!($this->getUser() == $clan->getCreator())) {
            throw new AccessDeniedException('Seul le créateur.la créatrice d\'un clan peut le modifier.');
        }

        $form = $this->createForm(ClanType::class, $clan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                "Les informations sur votre clan ont bien été modifiées!"
            );

            return $this->redirectToRoute('clan_index');
        }

        return $this->render('clan/edit.html.twig', [
            'clan' => $clan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Clan $clan
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, Clan $clan): Response
    {
        if (!($this->getUser() == $clan->getCreator())) {
            throw new AccessDeniedException('Seul le créateur.la créatrice d\'un clan peut le supprimer.');
        }
        if ($this->isCsrfTokenValid('delete' . $clan->getId(), $request->request->get('_token'))) {
            $entityManager->remove($clan);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Votre clan a bien été supprimé."
            );
        }

        return $this->redirectToRoute('clan_index');
    }

    /**
     * @Route(
     *     "/{id}/rejoindre",
     *     name="join",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ClanRepository $clanRepository
     * @return Response
     */
    public function join(
        Request $request,
        EntityManagerInterface $entityManager,
        ClanRepository $clanRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $clanId = $request->request->get('clanId');
        $submittedToken = $request->request->get('token');
        if (
            $this->isCsrfTokenValid('clan-join', $submittedToken) &&
            filter_var($clanId, FILTER_VALIDATE_INT)
        ) {
            $clan = $clanRepository->find($clanId);
            if ($clan && $user) {
                /* @phpstan-ignore-next-line */
                $clan->addMember($user);
                $entityManager->flush();
                return $this->redirectToRoute('clan_show', [
                    'id' => $clanId,
                ]);
            }
        }
        return $this->redirectToRoute('clan_index');
    }

    /**
     * @Route(
     *     "/{id}/quitter",
     *     name="leave",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ClanRepository $clanRepository
     * @return Response
     */
    public function leave(
        Request $request,
        EntityManagerInterface $entityManager,
        ClanRepository $clanRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $clanId = $request->request->get('clanId');
        $submittedToken = $request->request->get('token');
        if (
            $this->isCsrfTokenValid('clan-leave', $submittedToken) &&
            filter_var($clanId, FILTER_VALIDATE_INT)
        ) {
            $clan = $clanRepository->find($clanId);
            if ($clan && $user) {
                /* @phpstan-ignore-next-line */
                $clan->removeMember($user);
                $entityManager->flush();
                return $this->redirectToRoute('clan_show', [
                    'id' => $clanId,
                ]);
            }
        }
        return $this->redirectToRoute('clan_index');
    }

    /**
     * @Route(
     *     "/{id}/invitation",
     *     name="invite",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param MailerInterface $mailer
     * @param Clan $clan
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function invite(Request $request, MailerInterface $mailer, Clan $clan): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $emailAddress = $request->request->get('email');
        $submittedToken = $request->request->get('token');
        if (
            $this->isCsrfTokenValid('clan-invite', $submittedToken) &&
            filter_var($emailAddress, FILTER_VALIDATE_EMAIL)
        ) {
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to($emailAddress)
                ->subject('Invitation à rejoindre un clan Kyosc')
                ->html(
                    $this->renderView(
                        'email/clan-invitation.html.twig',
                        ['clan' => $clan, 'user' => $this->getUser()]
                    )
                );
            $mailer->send($email);
            $this->addFlash(
                'success',
                'Votre invitation a bien été envoyée à l\'adresse suivante ' . $emailAddress . '.'
            );
        } else {
            $this->addFlash('danger', 'L\'adresse email ' . $emailAddress . ' est invalide.');
        }
        return $this->redirectToRoute('clan_show', [
            'id' => $clan->getId(),
        ]);
    }
}

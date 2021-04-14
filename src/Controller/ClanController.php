<?php

namespace App\Controller;

use App\Entity\Clan;
use App\Form\ClanType;
use App\Repository\ClanRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/clan", name="clan_")
 */
class ClanController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
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
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Clan $clan): Response
    {
        return $this->render('clan/show.html.twig', [
            'clan' => $clan,
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
}

<?php

namespace App\Controller;

use App\Entity\Clan;
use App\Form\ClanType;
use App\Repository\ClanRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Clan $clan): Response
    {
        $form = $this->createForm(ClanType::class, $clan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('clan_index');
        }

        return $this->render('clan/edit.html.twig', [
            'clan' => $clan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Clan $clan): Response
    {
        if ($this->isCsrfTokenValid('delete' . $clan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($clan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('clan_index');
    }
}

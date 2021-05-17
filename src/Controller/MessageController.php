<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/message")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/{id}/edit", name="message_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_delete", methods={"DELETE"})
     */
    public function deleteClanComment(Request $request, Message $message): Response
    {
        $clanId = false;
        $challengeId = '';
        if ($message->getClan()) {
            /* @phpstan-ignore-next-line */
            $clanId = $message->getClan()->getId();
        } else {
            /* @phpstan-ignore-next-line */
            $challengeId = $message->getChallenge()->getId();
        }

        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();
        }

        if ($clanId) {
            if ($message->getIsPublic()) {
                return $this->redirectToRoute('clan_show', [
                    'id' => $clanId
                ]);
            } else {
                return $this->redirectToRoute('clan_my-clan', [
                    'id' => $clanId
                ]);
            }
        } else {
            return $this->redirectToRoute('challenge_show', [
                'id' => $challengeId
            ]);
        }
    }
}

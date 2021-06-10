<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/photo")
 */
class PictureController extends AbstractController
{
    /**
     * @Route("/{id}", name="picture_delete", methods={"DELETE"})
     * @param Request $request
     * @param Picture $picture
     * @return Response
     */
    public function deletePicture(Request $request, Picture $picture): Response
    {
        $clanId = false;
        $challengeId = '';
        if ($picture->getClan()) {
            /* @phpstan-ignore-next-line */
            $clanId = $picture->getClan()->getId();
        } else {
            /* @phpstan-ignore-next-line */
            $challengeId = $picture->getChallenge()->getId();
        }

        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();
        }

        if ($clanId) {
            return $this->redirectToRoute('clan_show', [
                'id' => $clanId
            ]);
        } else {
            return $this->redirectToRoute('challenge_show', [
                'id' => $challengeId
            ]);
        }
    }
}

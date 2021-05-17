<?php

namespace App\Controller;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/video")
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/{id}", name="video_delete", methods={"DELETE"})
     * @param Request $request
     * @param Video $video
     * @return Response
     */
    public function delete(Request $request, Video $video): Response
    {
        /* @phpstan-ignore-next-line */
        $challengeId = $video->getChallenge()->getId();

        if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('challenge_show', [
            'id' => $challengeId
        ]);
    }
}

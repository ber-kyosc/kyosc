<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Challenge;
use App\Entity\ChallengeSearch;
use App\Entity\Message;
use App\Entity\Sport;
use App\Entity\Video;
use App\Form\ChallengeSearchType;
use App\Form\ChallengeType;
use App\Form\MessageType;
use App\Form\VideoType;
use App\Repository\CategoryRepository;
use App\Repository\ChallengeRepository;
use App\Repository\SportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Knp\Component\Pager\PaginatorInterface;
use mysql_xdevapi\Exception;
use PhpParser\Node\Expr\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use DateTime;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route(
 *     "/aventure",
 *     name="challenge_"
 * )
 */
class ChallengeController extends AbstractController
{
    /** @Route(
     *     "/",
     *     name="index",
     *     methods={"GET"}
     * )
     * @param ChallengeRepository $challengeRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(
        ChallengeRepository $challengeRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $queryBuilder = $challengeRepository->findAllByDateQueryBuilder();
        return $this->createChallengeDisplay(
            $challengeRepository,
            $request,
            $paginator,
            $queryBuilder
        );
    }

    /**
     * @Route(
     *     "/nouveau",
     *     name="new",
     *     methods={"GET", "POST"}
     * )
     * @param SportRepository $sportRepository
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @return Response
     */
    public function new(
        SportRepository $sportRepository,
        CategoryRepository $categoryRepository,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sports = $sportRepository->findAll();
        $categories = $categoryRepository->findAll();
        $challenge = new Challenge();
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $challenge->setCreatedAt(new DateTime());
            $challenge->setUpdatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $challenge->setCreator($this->getUser());
            /* @phpstan-ignore-next-line */
            $challenge->addParticipant($this->getUser());
            $entityManager->persist($challenge);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Votre aventure a été correctement soumis et sera validée très prochainement !"
            );

            return $this->redirectToRoute('challenge_index');
        }
        return $this->render('challenge/new.html.twig', [
            'form' => $form->createView(),
            'sports' => $sports,
            'categories' => $categories
            ]);
    }

    /**
     * @Route(
     *     "/categorie/{name}",
     *     name="by_category",
     *     methods={"GET"},
     *     requirements={"name"="^[a-z-]+$"},
     * )
     * @param ChallengeRepository $challengeRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Category $category
     * @return Response
     */
    public function challengeByCategory(
        ChallengeRepository $challengeRepository,
        Request $request,
        PaginatorInterface $paginator,
        Category $category
    ): Response {
        $queryBuilder = $challengeRepository->findByCategoryQueryBuilder($category->getName());
        return $this->createChallengeDisplay(
            $challengeRepository,
            $request,
            $paginator,
            $queryBuilder,
            [ 'category' => $category ]
        );
    }

    /**
     * @Route(
     *     "/{slug}",
     *     name="by_sport",
     *     methods={"GET"},
     *     requirements={"slug"="^[a-z-]+$"},
     * )
     * @param ChallengeRepository $challengeRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Sport $sport
     * @return Response
     */
    public function challengeBySport(
        ChallengeRepository $challengeRepository,
        Request $request,
        PaginatorInterface $paginator,
        Sport $sport
    ): Response {
        $queryBuilder = $challengeRepository->findBySportQueryBuilder($sport->getSlug());
        return $this->createChallengeDisplay(
            $challengeRepository,
            $request,
            $paginator,
            $queryBuilder,
            [ 'sport' => $sport ]
        );
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
     * @param ChallengeRepository $challengeRepository
     * @return Response
     */
    public function join(
        Request $request,
        EntityManagerInterface $entityManager,
        ChallengeRepository $challengeRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $challengeId = $request->request->get('challengeId');
        $submittedToken = $request->request->get('token');
        if (
            $this->isCsrfTokenValid('challenge-join', $submittedToken) &&
            filter_var($challengeId, FILTER_VALIDATE_INT)
        ) {
            $challenge = $challengeRepository->find($challengeId);
            if ($challenge && $user) {
                /* @phpstan-ignore-next-line */
                $challenge->addParticipant($user);
                $entityManager->flush();
                return $this->redirectToRoute('challenge_show', [
                    'id' => $challengeId,
                ]);
            }
        }
        return $this->redirectToRoute('challenge_index');
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
     * @param ChallengeRepository $challengeRepository
     * @return Response
     */
    public function leave(
        Request $request,
        EntityManagerInterface $entityManager,
        ChallengeRepository $challengeRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $challengeId = $request->request->get('challengeId');
        $submittedToken = $request->request->get('token');
        if (
            $this->isCsrfTokenValid('challenge-leave', $submittedToken) &&
            filter_var($challengeId, FILTER_VALIDATE_INT)
        ) {
            $challenge = $challengeRepository->find($challengeId);
            if ($challenge && $user) {
                /* @phpstan-ignore-next-line */
                $challenge->removeParticipant($user);
                $entityManager->flush();
                return $this->redirectToRoute('challenge_show', [
                    'id' => $challengeId,
                ]);
            }
        }
        return $this->redirectToRoute('challenge_index');
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
     * @param Challenge $challenge
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function invite(Request $request, MailerInterface $mailer, Challenge $challenge): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $emailAddress = $request->request->get('email');
        $submittedToken = $request->request->get('token');
        if (
            $this->isCsrfTokenValid('challenge-invite', $submittedToken) &&
            filter_var($emailAddress, FILTER_VALIDATE_EMAIL)
        ) {
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to($emailAddress)
                ->subject('Invitation à une aventure Kyosc')
                ->html(
                    $this->renderView(
                        'email/challenge-invitation.html.twig',
                        ['challenge' => $challenge, 'user' => $this->getUser()]
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
        return $this->redirectToRoute('challenge_show', [
            'id' => $challenge->getId(),
        ]);
    }

    /**
     * @Route(
     *     "/{id}",
     *     name="show",
     *     methods={"GET", "POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Challenge $challenge
     * @param Request $request
     * @return Response
     */
    public function show(Challenge $challenge, Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        $video = new Video();
        $formVideo = $this->createForm(VideoType::class, $video);
        $formVideo->handleRequest($request);

        /* @phpstan-ignore-next-line */
        if ($form->isSubmitted() && $form->isValid() && $form->get('save-message')->isClicked()) {
            $entityManager = $this->getDoctrine()->getManager();
            $message->setChallenge($challenge);
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $message->setCreatedAt(new DateTime());
            $message->setUpdatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $message->setAuthor($this->getUser());
            $message->setIsPublic(true);
            $entityManager->persist($message);
            $entityManager->flush();
        }

        /* @phpstan-ignore-next-line */
        if ($formVideo->isSubmitted() && $formVideo->isValid() && $formVideo->get('save-video')->isClicked()) {
            $entityManager = $this->getDoctrine()->getManager();
            $video->setChallenge($challenge);
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $video->setCreatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $video->setAuthor($this->getUser());
            $url = $video->getLink();
            $youtubeVideoId = [];
            /* @phpstan-ignore-next-line */
            parse_str(parse_url($url, PHP_URL_QUERY), $youtubeVideoId);
            $video->setYoutubeId($youtubeVideoId['v']);
            $entityManager->persist($video);
            $entityManager->flush();
        }

        return $this->render('challenge/show.html.twig', [
            'challenge' => $challenge,
            'form' => $form->createView(),
            'formVideo' => $formVideo->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/{id}/edition",
     *     name="edit",
     *     methods={"GET", "POST", "PUT"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Challenge $challenge
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @return Response
     */
    public function edit(Challenge $challenge, CategoryRepository $categoryRepository, Request $request): Response
    {
        if (!($this->getUser() == $challenge->getCreator())) {
            throw new AccessDeniedException('Seul le créateur.la créatrice d\'une aventure peut la modifier.');
        }

        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        $categories = $categoryRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                "Les informations sur votre aventure ont bien été modifiées!"
            );

            return $this->redirectToRoute('profil_my_profil');
        }

        return $this->render('challenge/edit.html.twig', [
            'form' => $form->createView(),
            'challenge' => $challenge,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route(
     *     "/{id}",
     *     name="delete",
     *     methods={"DELETE"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Challenge $challenge
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, Challenge $challenge): Response
    {
        if (!($this->getUser() == $challenge->getCreator())) {
            throw new AccessDeniedException('Seul le créateur.la créatrice d\'une aventure peut la supprimer.');
        }
        if ($this->isCsrfTokenValid('delete' . $challenge->getId(), $request->request->get('_token'))) {
            $entityManager->remove($challenge);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Votre aventure a bien été supprimée."
            );
        }
        return $this->redirectToRoute("profil_my_profil");
    }

    /**
     * @param array<object> $options
     */
    private function createChallengeDisplay(
        ChallengeRepository $challengeRepository,
        Request $request,
        PaginatorInterface $paginator,
        QueryBuilder $query,
        array $options = []
    ): Response {
        $category = $options['category'] ?? null;
        $sport = $options['sport'] ?? null;

        $search = new ChallengeSearch();
        $maxParticipants = $challengeRepository->maxParticipants();
        $minParticipants = $challengeRepository->minParticipants();
        if ($minParticipants === $maxParticipants) {
            $maxParticipants++;
        }
        $minMaxDistance = $challengeRepository->minMaxDistance();
        $form = $this->createForm(ChallengeSearchType::class, $search);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $queryBuilder = $challengeRepository->searchChallenges($search);
            $sport = $form->getData()->getSport();
            $category = null;
        } else {
            $queryBuilder = $query;
        }
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            16 /*limit per page*/,
            ['wrap-queries' => true]
        );

        return $this->render('challenge/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
            'max' => $maxParticipants,
            'min' => $minParticipants,
            'minMaxDistance' => $minMaxDistance,
            'category' => $category,
            'sport' => $sport,
        ]);
    }
}

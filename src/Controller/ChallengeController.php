<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Challenge;
use App\Entity\ChallengeSearch;
use App\Entity\Clan;
use App\Entity\Invitation;
use App\Entity\JoinRequest;
use App\Entity\Message;
use App\Entity\Picture;
use App\Entity\Sport;
use App\Entity\Video;
use App\Form\ChallengeSearchType;
use App\Form\ChallengeType;
use App\Form\MessageType;
use App\Form\PictureType;
use App\Form\VideoType;
use App\Repository\CategoryRepository;
use App\Repository\ChallengeRepository;
use App\Repository\InvitationRepository;
use App\Repository\JoinRequestRepository;
use App\Repository\MessageRepository;
use App\Repository\RequestRepository;
use App\Repository\SportRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use Knp\Component\Pager\PaginatorInterface;
use mysql_xdevapi\Exception;
use PhpParser\Node\Expr\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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
     *     "/nouvelle",
     *     name="new",
     *     methods={"GET", "POST"}
     * )
     * @param SportRepository $sportRepository
     * @param CategoryRepository $categoryRepository
     * @param MailerInterface $mailer
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function new(
        SportRepository $sportRepository,
        CategoryRepository $categoryRepository,
        MailerInterface $mailer,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sports = $sportRepository->findAll();
        $categories = $categoryRepository->findAll();
        $challenge = new Challenge();
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $entityManager = $this->getDoctrine()->getManager();
            $challenge->setCreatedAt(new DateTime());
            $challenge->setUpdatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $challenge->setCreator($user);
            /* @phpstan-ignore-next-line */
            $challenge->addParticipant($user);
            $entityManager->persist($challenge);
            $entityManager->flush();
            if (!empty($challenge->getClans())) {
                foreach ($challenge->getClans() as $clan) {
                    foreach ($clan->getMembers() as $member) {
                        if ($member != $user) {
                            /* @phpstan-ignore-next-line */
                            $emailAddress = $member->getEmail();
                            $email = (new Email())
                                ->from($this->getParameter('mailer_from'))
                                ->to($emailAddress)
                                ->subject('Invitation à une aventure Kyosc')
                                ->html(
                                    $this->renderView(
                                        'email/challenge-invitation.html.twig',
                                        ['challenge' => $challenge, 'user' => $user]
                                    )
                                );
                            $mailer->send($email);
                            $invitation = new Invitation();
                            $invitation->setChallenge($challenge)
                                ->setCreatedAt(new DateTime())
                                ->setUpdatedAt(new DateTime())
                                ->setRecipient($emailAddress);
                            /* @phpstan-ignore-next-line */
                            $invitation->setCreator($user);
                            $invitation->setInvitedUser($member);
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($invitation);
                            $entityManager->flush();
                        }
                    }
                }
            }

            $this->addFlash(
                'success',
                "Bravo, votre aventure a bien été créée !"
            );

            return $this->redirectToRoute('challenge_show', [
                'id' => $challenge->getId(),
            ]);
        }
        return $this->render('challenge/new.html.twig', [
            'form' => $form->createView(),
            'sports' => $sports,
            'categories' => $categories,
            'challenge' => null,
            ]);
    }

    /**
     * @Route(
     *     "/nouvelle/{id}",
     *     name="new-from-clan",
     *     methods={"GET", "POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Clan $clan
     * @param SportRepository $sportRepository
     * @param CategoryRepository $categoryRepository
     * @param MailerInterface $mailer
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function newFromClan(
        Clan $clan,
        SportRepository $sportRepository,
        CategoryRepository $categoryRepository,
        MailerInterface $mailer,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sports = $sportRepository->findAll();
        $categories = $categoryRepository->findAll();
        $challenge = new Challenge();
        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $entityManager = $this->getDoctrine()->getManager();
            $challenge->setCreatedAt(new DateTime());
            $challenge->setUpdatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $challenge->setCreator($user);
            /* @phpstan-ignore-next-line */
            $challenge->addParticipant($user);
            $challenge->addClan($clan);
            $entityManager->persist($challenge);
            $entityManager->flush();
            foreach ($clan->getMembers() as $member) {
                if ($member != $user) {
                    /* @phpstan-ignore-next-line */
                    $emailAddress = $member->getEmail();
                    $email = (new Email())
                        ->from($this->getParameter('mailer_from'))
                        ->to($emailAddress)
                        ->subject('Invitation à une aventure Kyosc')
                        ->html(
                            $this->renderView(
                                'email/challenge-invitation.html.twig',
                                ['challenge' => $challenge, 'user' => $user]
                            )
                        );
                    $mailer->send($email);
                    $invitation = new Invitation();
                    $invitation->setChallenge($challenge)
                        ->setCreatedAt(new DateTime())
                        ->setUpdatedAt(new DateTime())
                        ->setRecipient($emailAddress);
                    /* @phpstan-ignore-next-line */
                    $invitation->setCreator($user);
                    $invitation->setInvitedUser($member);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($invitation);
                    $entityManager->flush();
                }
            }

            $this->addFlash(
                'success',
                "Bravo, votre aventure a bien été créée !"
            );

            return $this->redirectToRoute('challenge_show', [
                'id' => $challenge->getId(),
            ]);
        }
        return $this->render('challenge/new.html.twig', [
            'form' => $form->createView(),
            'fromClan' => $clan,
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
     * @param InvitationRepository $invitationRepository
     * @return Response
     */
    public function join(
        Request $request,
        EntityManagerInterface $entityManager,
        ChallengeRepository $challengeRepository,
        InvitationRepository $invitationRepository
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
                $invitations = $invitationRepository->findBy([
                    'challenge' => $challenge,
                    /* @phpstan-ignore-next-line */
                    'recipient' => $user->getEmail(),
                ]);
                if ($invitations) {
                    foreach ($invitations as $invitation) {
                        if (!$invitation->getIsAccepted() & !$invitation->getIsRejected()) {
                            $invitation->setIsAccepted(true)
                                ->setUpdatedAt(new DateTime());
                            $entityManager->persist($invitation);
                            $entityManager->flush();
                        }
                    }
                }
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
     *     "/{id}/demande-a-rejoindre",
     *     name="request-to-join",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param MailerInterface $mailer
     * @param Challenge $challenge
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function requestToJoin(
        Request $request,
        MailerInterface $mailer,
        Challenge $challenge
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $challengeId = $request->request->get('challengeId');
        $submittedToken = $request->request->get('token');
        $requestMessage = $request->request->get('requestMessage');
        if (
            $this->isCsrfTokenValid('challenge-request-to-join', $submittedToken) &&
            filter_var($challengeId, FILTER_VALIDATE_INT)
        ) {
            $emailAddress = $challenge->getCreator()->getEmail();
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to($emailAddress)
                ->subject('Demande à rejoindre l\'une de vos aventures')
                ->html(
                    $this->renderView(
                        'email/challenge-request.html.twig',
                        [
                            'challenge' => $challenge,
                            'user' => $user,
                            'requestMessage' => $requestMessage
                        ]
                    )
                );
            $mailer->send($email);
            $joinRequest = new JoinRequest();
            $joinRequest->setChallenge($challenge)
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
                ->setRequestedUser($challenge->getCreator());
            /* @phpstan-ignore-next-line */
            $joinRequest->setCreator($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($joinRequest);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre demande à bien été prise en compte et envoyée à l\'organisateur.trice du challenge'
            );
            return $this->redirectToRoute('challenge_show', [
                'id' => $challenge->getId(),
            ]);
        }
        return $this->redirectToRoute('challenge_index');
    }

    /**
     * @Route(
     *     "/{id}/accepter-demande",
     *     name="accept-request",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Challenge $challenge
     * @param MailerInterface $mailer
     * @param JoinRequestRepository $joinRequestRepository
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function acceptRequest(
        Request $request,
        EntityManagerInterface $entityManager,
        Challenge $challenge,
        MailerInterface $mailer,
        JoinRequestRepository $joinRequestRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $challengeCreator = $this->getUser();
        $joinRequestId = $request->request->get('requestId');
        $submittedToken = $request->request->get('token');
        if (
            $this->isCsrfTokenValid('challenge-accept-request', $submittedToken) &&
            filter_var($joinRequestId, FILTER_VALIDATE_INT)
        ) {
            $joinRequest = $joinRequestRepository->find($joinRequestId);
            if ($joinRequest && $challengeCreator) {
                /* @phpstan-ignore-next-line */
                $requestCreator = $joinRequest->getCreator();
                /* @phpstan-ignore-next-line */
                $challenge->addParticipant($requestCreator);
                $joinRequests = $joinRequestRepository->findBy([
                    'challenge' => $challenge,
                    /* @phpstan-ignore-next-line */
                    'creator' => $requestCreator,
                ]);
                if ($joinRequests) {
                    foreach ($joinRequests as $joinRequest) {
                        if (!$joinRequest->getIsAccepted() & !$joinRequest->getIsRejected()) {
                            $joinRequest->setIsAccepted(true)
                                ->setUpdatedAt(new DateTime());
                            $entityManager->persist($joinRequest);
                            $entityManager->flush();
                        }
                    }
                }
                $entityManager->flush();
                /* @phpstan-ignore-next-line */
                $emailAddress = $requestCreator->getEmail();
                $email = (new Email())
                    ->from($this->getParameter('mailer_from'))
                    ->to($emailAddress)
                    ->subject('Demande acceptée !')
                    ->html(
                        $this->renderView(
                            'email/challenge-joining-confirmation.html.twig',
                            [
                                'challenge' => $challenge,
                                'user' => $requestCreator
                            ]
                        )
                    );
                $mailer->send($email);

                return $this->redirectToRoute('challenge_show', [
                    'id' => $challenge->getId(),
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
     *     "/{id}/refuser",
     *     name="decline",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param EntityManagerInterface $entityManager
     * @param Challenge $challenge
     * @param InvitationRepository $invitationRepository
     * @return Response
     */
    public function decline(
        EntityManagerInterface $entityManager,
        Challenge $challenge,
        InvitationRepository $invitationRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $invitations = $invitationRepository->findBy([
            'challenge' => $challenge,
            /* @phpstan-ignore-next-line */
            'recipient' => $user->getEmail(),
        ]);
        if ($invitations) {
            foreach ($invitations as $invitation) {
                if (!$invitation->getIsAccepted() & !$invitation->getIsRejected()) {
                    $invitation->setIsRejected(true)
                        ->setUpdatedAt(new DateTime());
                    $entityManager->persist($invitation);
                    $entityManager->flush();
                }
            }
        }

        return $this->redirectToRoute('challenge_show', [
            'id' => $challenge->getId(),
        ]);
    }

    /**
     * @Route(
     *     "/{id}/refuser-demande",
     *     name="decline-request",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param EntityManagerInterface $entityManager
     * @param Challenge $challenge
     * @param JoinRequestRepository $joinRequestRepository
     * @return Response
     */
    public function declineRequest(
        EntityManagerInterface $entityManager,
        Challenge $challenge,
        JoinRequestRepository $joinRequestRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $creator = $this->getUser();
        $joinRequests = $joinRequestRepository->findBy([
            'challenge' => $challenge,
            /* @phpstan-ignore-next-line */
            'requestedUser' => $creator,
        ]);
        if ($joinRequests) {
            foreach ($joinRequests as $joinRequest) {
                if (!$joinRequest->getIsAccepted() & !$joinRequest->getIsRejected()) {
                    $joinRequest->setIsRejected(true)
                        ->setUpdatedAt(new DateTime());
                    $entityManager->persist($joinRequest);
                    $entityManager->flush();
                }
            }
        }

        return $this->redirectToRoute('challenge_show', [
            'id' => $challenge->getId(),
        ]);
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
     * @param UserRepository $userRepository
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function invite(
        Request $request,
        MailerInterface $mailer,
        Challenge $challenge,
        UserRepository $userRepository
    ): Response {
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
            $invitation = new Invitation();
            $invitation->setChallenge($challenge)
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
                ->setRecipient($emailAddress);
                /* @phpstan-ignore-next-line */
            $invitation->setCreator($this->getUser());
            $targetUser = $userRepository->findOneBy(['email' => $emailAddress]);
            if ($targetUser) {
                $invitation->setInvitedUser($targetUser);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invitation);
            $entityManager->flush();
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
    public function show(Challenge $challenge, MessageRepository $messageRepository, Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        $video = new Video();
        $formVideo = $this->createForm(VideoType::class, $video);
        $formVideo->handleRequest($request);

        $picture = new Picture();
        $formPicture = $this->createForm(PictureType::class, $picture);
        $formPicture->handleRequest($request);

        /* @phpstan-ignore-next-line */
        if ($form->isSubmitted() && $form->isValid()) {
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
            $data = $messageRepository->find($message->getId());
            return $this->json($data, Response::HTTP_OK, [], [
                'groups' => ['message', 'message_author']
            ]);
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
            if (!is_null($url)) {
                $youtubeVideoId = [];
                // phpcs:ignore
                $youtubeRegex = "/^(?:https?:)?(?:\/\/)?(?:youtu\.be\/|(?:www\.|m\.)?youtube\.com\/(?:watch|v|embed)(?:\.php)?(?:\?.*v=|\/))([a-zA-Z0-9\_-]{7,15})(?:[\?&][a-zA-Z0-9\_-]+=[a-zA-Z0-9\_-]+)*$/";
                preg_match($youtubeRegex, $url, $youtubeVideoId);
                if (!empty($youtubeVideoId)) {
                    $video->setYoutubeId($youtubeVideoId[1]);
                    $entityManager->persist($video);
                    $entityManager->flush();
                } else {
                    $this->addFlash(
                        'danger',
                        "L'URL saisit ne correspond pas à une URL Youtube valide"
                    );
                }
            }
        }

        /* @phpstan-ignore-next-line */
        if ($formPicture->isSubmitted() && $formPicture->isValid() && $formPicture->get('save-picture')->isClicked()) {
            $entityManager = $this->getDoctrine()->getManager();
            $picture->setChallenge($challenge);
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $picture->setCreatedAt(new DateTime());
            $picture->setUpdatedAt(new DateTime());
            /* @phpstan-ignore-next-line */
            $picture->setAuthor($this->getUser());
            $entityManager->persist($picture);
            $entityManager->flush();
        }

        return $this->render('challenge/show.html.twig', [
            'challenge' => $challenge,
            'form' => $form->createView(),
            'formVideo' => $formVideo->createView(),
            'formPicture' => $formPicture->createView(),
        ]);
    }

    /**
     * @Route(
     *     "/{id}/demande-aide-organisation",
     *     name="help-organise",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param MailerInterface $mailer
     * @param Challenge $challenge
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function helpOrganise(
        Request $request,
        MailerInterface $mailer,
        Challenge $challenge
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $challengeId = $request->request->get('challengeId');
        $submittedToken = $request->request->get('token');
        if (
            $this->isCsrfTokenValid('challenge-help-organise', $submittedToken) &&
            filter_var($challengeId, FILTER_VALIDATE_INT)
        ) {
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('bertrand@kyosc.com')
                ->subject('Demande de co-organisation aventure !')
                ->html(
                    $this->renderView(
                        'email/help-organise.html.twig',
                        [
                            'challenge' => $challenge,
                            'user' => $user,
                        ]
                    )
                );
            $mailer->send($email);
            $this->addFlash(
                'success',
                'Votre demande à bien été prise en compte et envoyée à un administrateur KYOSC'
            );
            return $this->redirectToRoute('challenge_show', [
                'id' => $challenge->getId(),
            ]);
        }
        return $this->redirectToRoute('challenge_index');
    }

    /**
     * @Route("/handleSearch/{_query?}", name="handle_search", methods={"POST", "GET"})
     * @param ChallengeRepository $challengeRepository
     * @return JsonResponse
     */
    public function handleSearchRequest(ChallengeRepository $challengeRepository)
    {
        if ($_POST['_query']) {
            $data = $challengeRepository->findLikeTitle($_POST['_query']);
        } else {
            $data = [];
        }

        return $this->json($data, Response::HTTP_OK, [], [
            'groups' => 'challenge_base'
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

            return $this->redirectToRoute('challenge_show', [
                'id' => $challenge->getId(),
            ]);
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

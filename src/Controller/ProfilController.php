<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditEmailType;
use App\Form\EditProfilType;
use App\Repository\CategoryRepository;
use App\Repository\ChallengeRepository;
use App\Repository\SportRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route ("/profil", name="profil_")
 */
class ProfilController extends AbstractController
{

    private UserPasswordEncoderInterface $passwordEncoder;
    private EmailVerifier $emailVerifier;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EmailVerifier $emailVerifier)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route ("/", name="index")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository)
    {
        $users = $userRepository->findBy([], ['firstName' => 'ASC']);
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/mon-profil", name="my_profil")
     * @param CategoryRepository $categoryRepository
     * @param ChallengeRepository $challengeRepository
     * @param Request $request
     * @return Response
     */
    public function myProfil(
        CategoryRepository $categoryRepository,
        ChallengeRepository $challengeRepository,
        Request $request
    ): Response {
        /* @phpstan-ignore-next-line */
        $user = $this->getUser();
        $challenges = $challengeRepository->findBy(
        /* @phpstan-ignore-next-line */
            ['creator' => $user->getId()],
            ['dateStart' => 'DESC']
        );
        $comingChallenges = [];
        $pastChallenges = [];
        foreach ($challenges as $challenge) {
            if ($challenge->getDateStart() < new DateTime()) {
                $pastChallenges[] = $challenge;
            } else {
                array_unshift($comingChallenges, $challenge);
            }
        }
        $otherChallenges = [];
        $pastOtherChallenges = [];
        /* @phpstan-ignore-next-line */
        foreach ($user->getChallenges() as $challenge) {
            if ($challenge->getCreator() != $user) {
                if ($challenge->getDateStart() < new DateTime()) {
                    $pastOtherChallenges[] = $challenge;
                } else {
                    array_unshift($otherChallenges, $challenge);
                }
            }
        }

        $categories = $categoryRepository->findAll();
        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /* @phpstan-ignore-next-line */
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié');
            return $this->redirectToRoute('profil_my_profil');
        }
        return $this->render('profil/show.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'challenges' => $challenges,
            'user' => $user,
            'pastChallenges' => $pastChallenges,
            'comingChallenges' => $comingChallenges,
            'otherChallenges' => $otherChallenges,
            'pastOtherChallenges' => $pastOtherChallenges,
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"}, requirements={"id"="^\d+$"})
     * @param User $user
     * @param ChallengeRepository $challengeRepository
     * @return Response
     */
    public function show(
        User $user,
        ChallengeRepository $challengeRepository
    ): Response {
        $challenges = $challengeRepository->findBy(
            ['creator' => $user->getId()],
            ['dateStart' => 'DESC']
        );
        $comingChallenges = [];
        $pastChallenges = [];
        foreach ($challenges as $challenge) {
            if ($challenge->getDateStart() < new DateTime()) {
                $pastChallenges[] = $challenge;
            } else {
                array_unshift($comingChallenges, $challenge);
            }
        }
        $sports = $user->getFavoriteSports();

        return $this->render('profil/user.html.twig', [
            'user' => $user,
            'sports' => $sports,
            'comingChallenges' => $comingChallenges,
            'pastChallenges' => $pastChallenges,

        ]);
    }

    /**
     * @Route("/mail", name="edit_email")
     * @param Request $request
     * @return Response
     */
    public function editEmail(Request $request): Response
    {
        $form = $this->createForm(EditEmailType::class, $this->getUser());
        $form->handleRequest($request);
        $error = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $mdp = $request->get('password');
            /* @phpstan-ignore-next-line */
            $user = $this->getUser();
            /* @phpstan-ignore-next-line */
            if ($request->isMethod('POST') && ($this->passwordEncoder->isPasswordValid($user, $mdp))) {
                $entityManager = $this->getDoctrine()->getManager();
                /* @phpstan-ignore-next-line */
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre email a bien été modifié');
                return $this->redirectToRoute('profil_my_profil');
            } else {
                $this->addFlash('danger', 'Vous avez entré un mauvais mot de passe. Vous avez été déconnecté
                 par mesure de sécurité. Votre email n\'a pas été modifié.');
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('profil/editEmail.html.twig', ['form' => $form->createView(), 'error' => $error]);
    }

    /**
     * @Route(
     *     "/{id}/contacter",
     *     name="contact-user",
     *     methods={"POST"},
     *     requirements={"id"="^\d+$"},
     * )
     * @param Request $request
     * @param MailerInterface $mailer
     * @param User $user
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function requestToJoin(
        Request $request,
        MailerInterface $mailer,
        User $user
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $sender = $this->getUser();
        $submittedToken = $request->request->get('token');
        $contactMessage = $request->request->get('contactMessage');
        if ($this->isCsrfTokenValid('contact-user', $submittedToken)) {
            $emailAddress = $user->getEmail();
            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to($emailAddress)
                ->subject('Message de la part d\'un membre KYOSC')
                ->html(
                    $this->renderView(
                        'email/contact-user.html.twig',
                        [
                            'sender' => $sender,
                            'user' => $user,
                            'contactMessage' => $contactMessage
                        ]
                    )
                );
            $mailer->send($email);
            $this->addFlash(
                'success',
                'Votre message à bien été envoyé à ' . $user->getFirstName()
            );
            return $this->redirectToRoute('profil_show', [
                'id' => $user->getId(),
            ]);
        }
        return $this->redirectToRoute('profil_index');
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        if (!($this->getUser() == $user)) {
            throw new AccessDeniedException('Seul le détenteur d\'un compte peut le supprimer.');
        }
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Votre compte a bien été supprimé."
            );
        }

        return $this->redirectToRoute('home');
    }
}

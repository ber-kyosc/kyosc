<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\InvitationRepository;
use App\Security\EmailVerifier;
use DateTimeImmutable;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/inscription", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param InvitationRepository $invitationRepository
     * @return Response
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        InvitationRepository $invitationRepository
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles(['ROLE_AUTHENTICATE_USER']);

            $entityManager = $this->getDoctrine()->getManager();
            $user->setUpdatedAt(new DateTimeImmutable('now'));

            $invitationsReceived = $invitationRepository->findBy([
                'recipient' => $user->getEmail(),
                'isAccepted' => false,
                'isRejected' => false,
            ]);
            if ($invitationsReceived) {
                foreach ($invitationsReceived as $invitation) {
                    $user->addInvitationsReceived($invitation);
                }
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@kyosc.com', 'Ne pas répondre'))
                    ->to($user->getEmail())
                    ->subject('KYOSC - veuillez confirmer votre mail')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $this->addFlash(
                'success',
                "Un mail de confirmation vous a été envoyé à l'adresse " . $user->getEmail()
            );
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verification/mail", name="app_verify_email")
     * @param Request $request
     * @return Response|null
     */
    public function verifyUserEmail(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /* @phpstan-ignore-next-line */
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre mail a bien été vérifié !');

        return $this->redirectToRoute('home');
    }

    /**
     * @Route ("/verification/envoi", name="app_send_verify_email")
     * @return Response
     */
    public function sendNewVerifyEmail(): Response
    {
        $user = $this->getUser();
        /* @phpstan-ignore-next-line */
        $email = $user->getEmail();
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,  /* @phpstan-ignore-line */
            (new TemplatedEmail())
                ->from(new Address('no-reply@kyosc.com', 'Ne pas répondre'))
                ->to($email)
                ->subject('KYOSC - veuillez confirmer votre mail')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        $this->addFlash('success', 'Un mail vient de vous être envoyé à l\'adresse ' . $email);
        return $this->redirectToRoute('home');
    }
}

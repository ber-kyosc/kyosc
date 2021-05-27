<?php

namespace App\EventListener;

use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private EntityManagerInterface $entityManager;

    private InvitationRepository $invitationRepository;

    public function __construct(EntityManagerInterface $entityManager, InvitationRepository $invitationRepository)
    {
        $this->entityManager = $entityManager;
        $this->invitationRepository = $invitationRepository;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        $invitationsReceived = $this->invitationRepository->findBy([
            /* @phpstan-ignore-next-line */
            'recipient' => $user->getEmail(),
            'isAccepted' => false,
            'isRejected' => false,
        ]);
        if ($invitationsReceived) {
            foreach ($invitationsReceived as $invitation) {
                /* @phpstan-ignore-next-line */
                $user->addInvitationsReceived($invitation);
            }
        }
        /* @phpstan-ignore-next-line */
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}

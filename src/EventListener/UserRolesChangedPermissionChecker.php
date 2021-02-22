<?php

namespace App\EventListener;

/* @phpstan-ignore-next-line */
use _HumbugBox39a196d4601e\Symfony\Component\Finder\Exception\AccessDeniedException;
use App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage;

class UserRolesChangedPermissionChecker
{

    private UsageTrackingTokenStorage $tokenStorage;

    public function __construct(UsageTrackingTokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    // the listener methods receive an argument which gives you access to
    // both the entity object of the event and the entity manager itself
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this listener only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof User) {
            return;
        }

        if ($args->hasChangedField('roles')) {
            /* @phpstan-ignore-next-line */
            $user = $this->tokenStorage->getToken()->getUser();
            /* @phpstan-ignore-next-line */
            if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                /* @phpstan-ignore-next-line */
                throw new AccessDeniedException('Vous n\'avez pas les droits suffisants');
            }
        }

        //$entityManager = $args->getObjectManager();
        // ... do something with the Product entity
    }
}

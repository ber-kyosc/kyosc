<?php

namespace App\Event;

use EasyCorp\Bundle\EasyAdminBundle\Event\StoppableEventTrait;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class BeforeEntityDeletedEvent
{
    use StoppableEventTrait;

    private string $entityInstance;

    public function __construct(string $entityInstance)
    {
        $this->entityInstance = $entityInstance;
    }

    public function getEntityInstance(): string
    {
        return $this->entityInstance;
    }
}

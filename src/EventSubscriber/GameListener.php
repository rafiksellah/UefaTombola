<?php

// src/EventSubscriber/GameListener.php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use App\Entity\Game;

class GameListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Game) {
            $now = new \DateTime('now');
            $entity->setCreatedAt($now);
        }
    }
}

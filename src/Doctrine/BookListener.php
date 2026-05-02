<?php

namespace App\Doctrine;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::preUpdate)]
class BookListener
{
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Book) {
            return;
        }

        $entity->setUpdatedAt(new \DateTimeImmutable());
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $book = new Book();
        $book->setPrice(1000);
        $book->setTitle('Alice au pays des merveilles');
        $book->setResume('Alice au pays des merveilles');

        $manager->persist($book);
        $manager->flush();

        $manager->flush();
    }
}

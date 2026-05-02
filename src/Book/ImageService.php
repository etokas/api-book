<?php

namespace App\Book;

use App\Entity\Book;
use App\Image\UploadInterface;
use App\Repository\BookRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    public function __construct(
        private readonly UploadInterface $uploadService,
        private readonly BookRepository $bookRepository,
        private readonly ImageRepository $imageRepository,
    ) {
    }

    public function createOrUpdate(UploadedFile $image, Book $book): Book
    {
        $currentImage = $book->getImage();
        if ($currentImage && sha1_file($image->getPathname()) === $currentImage->getHash()) {

            return $book;
        }

        $imageEntity = $this->uploadService->upload($image);

        if ($currentImage) {
            $book->setImage(null);
            $this->imageRepository->remove($currentImage);
        }

        $imageEntity->setBook($book);

        $this->bookRepository->flush();

        return $book;
    }
}
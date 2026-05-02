<?php

namespace App\Book\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Book;
use App\Image\LocalUpload;
use App\Repository\BookRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly LocalUpload $localUpload,
        private readonly BookRepository $bookRepository,
        private readonly ImageRepository $imageRepository,
    ) {
        
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Book
    {
       if (!$data instanceof Book) {
           throw new NotFoundHttpException('Book not found');
       }

       $image = $this->requestStack->getCurrentRequest()->files->get('image');

       if (!$image instanceof UploadedFile) {
           throw new NotFoundHttpException('Image upload failed');
       }

       $currentImage = $data->getImage();
       if ($currentImage && sha1_file($image->getPathname()) === $currentImage->getHash()) {

           return $data;
       }

        try {
            $imageEntity = $this->localUpload->upload($image);

            if ($currentImage) {
                $data->setImage(null);
                $this->imageRepository->remove($currentImage);
            }

            $imageEntity->setBook($data);

            $this->bookRepository->flush();
        } catch (UploadException $e) {
            throw new \RuntimeException($e->getMessage());
        }

       return $data;
    }
}
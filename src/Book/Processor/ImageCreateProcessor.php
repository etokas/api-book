<?php

namespace App\Book\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Book\ImageService;
use App\Entity\Book;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImageCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ImageService $imageService,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Book
    {
        if (!$data instanceof Book) {
            throw new NotFoundHttpException('Book not found');
        }

        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            throw new NotFoundHttpException('Current request not found');
        }

        $image = $currentRequest->files->get('image');

        if (!$image instanceof UploadedFile) {
            throw new NotFoundHttpException('Image upload failed');
        }

        return $this->imageService->createOrUpdate($image, $data);
    }
}
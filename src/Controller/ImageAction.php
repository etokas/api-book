<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/images/{path}', methods: ['GET'])]
class ImageAction
{

    public function __construct(
        #[Autowire('%kernel.project_dir%/data/images')]
        private readonly string $dataImageDir,
    ) {
    }

    public function __invoke(Image $image): Response
    {
        if (!file_exists($this->dataImageDir . '/' . $image->getPath())) {
            throw new NotFoundHttpException('File not found');
        }

        return new BinaryFileResponse($this->dataImageDir . '/' . $image->getPath());
    }
}